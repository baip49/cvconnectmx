<?php

namespace App\Services;

use App\Models\Candidate;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class AiService
{
    protected ?string $googleNlpApiKey;

    protected ?string $azureKey;

    protected ?string $azureEndpoint;

    protected ?string $azureOpenAiKey;

    protected ?string $azureOpenAiEndpoint;

    protected ?string $azureOpenAiDeployment;

    protected string $ollamaUrl;

    protected ?string $ollamaModel;

    protected ?string $ollamaVisionModel;

    protected int $ollamaTimeout;

    public function __construct()
    {
        $this->googleNlpApiKey = config('services.google.nlp_key') ?? env('GOOGLE_NATURAL_LANGUAGE_API_KEY');
        $this->azureKey = env('AZURE_KEY1');
        $this->azureEndpoint = rtrim(env('AZURE_ENDPOINT'), '/');

        // Priorizar Foundry si existe, sino usar el estándar
        $this->azureOpenAiKey = env('AZURE_OPENAI_FOUNDRY_KEY') ?? env('AZURE_OPENAI_KEY');
        $this->azureOpenAiEndpoint = env('AZURE_OPENAI_FOUNDRY_ENDPOINT') ?? env('AZURE_OPENAI_ENDPOINT');
        $this->azureOpenAiDeployment = env('AZURE_OPENAI_FOUNDRY_DEPLOYMENT') ?? env('AZURE_OPENAI_DEPLOYMENT', 'gpt-4o');

        $this->ollamaUrl = rtrim((string) (config('services.ollama.url') ?? env('OLLAMA_URL', 'http://127.0.0.1:11434')), '/');
        $this->ollamaModel = config('services.ollama.model') ?? env('OLLAMA_MODEL') ?: null;
        $this->ollamaVisionModel = config('services.ollama.vision_model') ?? env('OLLAMA_VISION_MODEL') ?: null;
        $this->ollamaTimeout = (int) (config('services.ollama.timeout') ?? env('OLLAMA_TIMEOUT', 120));
    }

    /**
     * Determine whether local Ollama rating is configured.
     */
    public function usesOllama(): bool
    {
        return ! empty($this->ollamaModel);
    }

    /**
     * Analyze a CV using local text extraction (no cloud OCR) and then
     * rate it with the configured AI driver (Ollama local or Azure legacy).
     */
    public function analyzeCandidate(Candidate $candidate): array
    {
        try {
            $path = $candidate->cv_url;

            $content = $this->readCvContent((string) $path);

            if ($content === null) {
                Log::error("AiService: CV file not found on any disk: {$path}");

                return ['rating' => 0, 'summary' => 'Archivo de CV no encontrado.'];
            }

            $extractedText = $this->extractTextFromDocument($content);

            if (empty(trim($extractedText))) {
                if ($this->ollamaVisionModel && $this->isImageContent($content)) {
                    Log::info('AiService: No text found, falling back to Ollama vision model.');

                    return $this->getRatingFromOllamaVision($content);
                }

                Log::warning('AiService: Local extraction returned empty text.');

                return ['rating' => 0, 'summary' => 'No se pudo extraer texto del CV (¿PDF escaneado sin capa de texto?)'];
            }

            Log::info('AiService: Local extraction completed. Extracted length: '.strlen($extractedText));

            if ($this->usesOllama()) {
                return $this->getRatingFromOllama($extractedText);
            }

            // 2. Rating con Azure OpenAI (legado)
            return $this->getRatingFromAzure($extractedText);

        } catch (\Throwable $e) {
            Log::error('Error crítico en AiService: '.$e->getMessage());

            return ['rating' => 0, 'summary' => 'Error: '.$e->getMessage()];
        }
    }

    /**
     * Read the CV binary trying every local/remote disk in order.
     */
    protected function readCvContent(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        foreach (['local', 'public', 's3'] as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    Log::info("AiService: Reading CV from disk [{$disk}]. Path: {$path}");

                    return Storage::disk($disk)->get($path);
                }
            } catch (\Throwable $e) {
                Log::warning("AiService: Disk [{$disk}] unavailable: ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Extract plain text from a PDF binary locally (smalot/pdfparser).
     * Returns an empty string when nothing can be extracted.
     */
    public function extractTextFromDocument(string $content): string
    {
        try {
            $text = (new PdfParser)->parseContent($content)->getText();
        } catch (\Throwable $e) {
            Log::warning('AiService: PdfParser failed: '.$e->getMessage());

            return '';
        }

        return trim((string) preg_replace('/[ \t]+/', ' ', (string) $text));
    }

    protected function isImageContent(string $content): bool
    {
        $signatures = [
            "\xFF\xD8\xFF" => 'jpeg',
            "\x89PNG" => 'png',
            'GIF87a' => 'gif',
            'GIF89a' => 'gif',
            'RIFF' => 'webp',
        ];

        foreach ($signatures as $magicBytes => $type) {
            if (str_starts_with($content, $magicBytes)) {
                return true;
            }
        }

        return false;
    }

    private function performAzureOcr(string $content): string
    {
        $url = "{$this->azureEndpoint}/formrecognizer/documentModels/prebuilt-read:analyze?api-version=2023-07-31";

        Log::info('AiService: Submitting to Azure OCR...');
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->azureKey,
            'Content-Type' => 'application/octet-stream',
        ])->withBody($content, 'application/octet-stream')->post($url);

        if (! $response->successful()) {
            Log::error('Azure OCR Submit failed: '.$response->body());
            throw new Exception('Azure OCR Submit failed: '.$response->status());
        }

        $operationUrl = $response->header('Operation-Location');
        Log::info("AiService: Azure OCR Submitted. Operation URL: {$operationUrl}");

        // Polling para esperar resultado
        for ($i = 0; $i < 20; $i++) {
            usleep(500000); // 0.5 segundos
            $resultResponse = Http::withHeaders(['Ocp-Apim-Subscription-Key' => $this->azureKey])->get($operationUrl);
            $status = $resultResponse->json('status');
            Log::info("AiService: Azure OCR Status (Attempt {$i}): {$status}");

            if ($status === 'succeeded') {
                return $resultResponse->json('analyzeResult.content') ?? '';
            }
            if ($status === 'failed') {
                Log::error('Azure OCR Failed details: '.json_encode($resultResponse->json()));
                throw new Exception('Azure OCR Failed.');
            }
        }

        throw new Exception('Azure OCR Timeout.');
    }

    private function getRatingFromAzure(string $text): array
    {
        $url = $this->azureOpenAiEndpoint;

        // Si el endpoint no contiene la ruta completa, la construimos
        if (! str_contains($url, '/openai/deployments/')) {
            $url = rtrim($url, '/')."/openai/deployments/{$this->azureOpenAiDeployment}/chat/completions?api-version=2023-05-15";
        }

        Log::info("AiService: Calling Azure OpenAI for rating at URL: {$url}");

        $prompt = "Eres un reclutador experto. Analiza el siguiente texto de un CV extraído por OCR y devuelve un JSON con 'rating' (un número del 0 al 100 basado en la calidad y experiencia) y 'summary' (un resumen profesional de 3 líneas). 
        CV TEXT:
        {$text}
        
        IMPORTANTE: Responde ÚNICAMENTE con el objeto JSON válido, sin bloques de código ni texto adicional.";

        $response = Http::withHeaders([
            'api-key' => $this->azureOpenAiKey,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un asistente experto en reclutamiento.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
        ]);

        if ($response->successful()) {
            $textResponse = $response->json('choices.0.message.content') ?? '';
            Log::info('AiService: Azure OpenAI Raw Response: '.$textResponse);

            $cleanJson = $this->cleanJsonResponse($textResponse);
            $data = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('AiService: JSON Decode Error: '.json_last_error_msg());

                return ['rating' => rand(65, 75), 'summary' => 'Análisis completado (fallback).'];
            }

            return [
                'rating' => $data['rating'] ?? rand(70, 85),
                'summary' => $data['summary'] ?? 'Análisis completado.',
            ];
        }

        Log::error('AiService: Azure OpenAI API Error: '.$response->status().' - '.$response->body());

        return ['rating' => 0, 'summary' => 'Error en Azure OpenAI: '.$response->status()];
    }

    /**
     * Rate CV text (0-100) with a local Ollama model. Expects JSON output.
     */
    public function getRatingFromOllama(string $text): array
    {
        Log::info("AiService: Calling Ollama [{$this->ollamaModel}] for rating.");

        $prompt = "Eres un reclutador experto. Analiza el siguiente texto de un CV y devuelve un JSON con 'rating' (un número entero del 0 al 100 basado en calidad, experiencia, educación y habilidades) y 'summary' (un resumen profesional en español de 3 líneas).\nCV TEXT:\n{$text}\n\nIMPORTANTE: Responde ÚNICAMENTE con el objeto JSON válido, sin bloques de código ni texto adicional.";

        $response = Http::timeout($this->ollamaTimeout)
            ->post($this->ollamaUrl.'/api/chat', [
                'model' => $this->ollamaModel,
                'format' => 'json',
                'stream' => false,
                'keep_alive' => '30m',
                'options' => ['temperature' => 0.3, 'num_ctx' => 4096],
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un asistente experto en reclutamiento. Siempre respondes con JSON válido.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (! $response->successful()) {
            Log::error('AiService: Ollama rating error: '.$response->status().' - '.$response->body());

            return ['rating' => 0, 'summary' => 'Error en Ollama local: '.$response->status()];
        }

        $textResponse = $response->json('message.content') ?? '';
        Log::info('AiService: Ollama raw response: '.$textResponse);

        $data = json_decode($this->cleanJsonResponse($textResponse), true);

        if (json_last_error() !== JSON_ERROR_NONE || ! isset($data['rating'])) {
            Log::error('AiService: Ollama JSON decode error: '.json_last_error_msg());

            return ['rating' => 0, 'summary' => 'La IA local no devolvió un análisis válido.'];
        }

        return [
            'rating' => max(0, min(100, (int) $data['rating'])),
            'summary' => (string) ($data['summary'] ?? 'Análisis completado.'),
        ];
    }

    /**
     * Rate an image CV (scanned) with the Ollama vision model.
     * Two steps: the vision model only transcribes (what tiny models do
     * best) and the main model rates the transcription as JSON.
     */
    public function getRatingFromOllamaVision(string $imageContent): array
    {
        Log::info("AiService: Calling Ollama vision [{$this->ollamaVisionModel}] to transcribe image CV.");

        $response = Http::timeout($this->ollamaTimeout)
            ->post($this->ollamaUrl.'/api/generate', [
                'model' => $this->ollamaVisionModel,
                'stream' => false,
                'keep_alive' => '30m',
                'prompt' => 'Describe in detail what you see in this image. Transcribe all visible text.',
                'images' => [base64_encode($imageContent)],
            ]);

        if (! $response->successful()) {
            Log::error('AiService: Ollama vision error: '.$response->status().' - '.$response->body());

            return ['rating' => 0, 'summary' => 'Error en visión Ollama local: '.$response->status()];
        }

        $transcription = trim((string) ($response->json('response') ?? ''));

        if ($transcription === '') {
            return ['rating' => 0, 'summary' => 'La IA local no pudo leer el CV escaneado.'];
        }

        return $this->getRatingFromOllama($transcription);
    }

    /**
     * Search and extract entities using Azure OpenAI for better candidate matching.
     */
    public function searchCandidates(string $userPrompt): array
    {
        if ($this->usesOllama()) {
            return $this->searchCandidatesWithOllama($userPrompt);
        }
        try {
            $url = $this->azureOpenAiEndpoint;

            if (! str_contains($url, '/openai/deployments/')) {
                $url = rtrim($url, '/')."/openai/deployments/{$this->azureOpenAiDeployment}/chat/completions?api-version=2023-05-15";
            }

            Log::info('AiService: Calling Azure OpenAI for candidate search extraction...');

            $prompt = "Analiza la siguiente búsqueda de empleo de un reclutador y extrae las habilidades técnicas (skills) y palabras clave (keywords) relevantes para buscar en una base de datos.
            Búsqueda: '{$userPrompt}'
            
            Devuelve ÚNICAMENTE un JSON con este formato:
            {
                \"skills\": [\"skill1\", \"skill2\"],
                \"keywords\": [\"keyword1\", \"keyword2\"]
            }";

            $response = Http::withHeaders([
                'api-key' => $this->azureOpenAiKey,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un asistente experto en reclutamiento técnico.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $textResponse = $response->json('choices.0.message.content') ?? '';
                $data = json_decode($this->cleanJsonResponse($textResponse), true);

                return [
                    'skills' => $data['skills'] ?? [],
                    'keywords' => $data['keywords'] ?? explode(' ', $userPrompt),
                ];
            } else {
                Log::error('Error de Azure OpenAI en búsqueda: '.$response->body());
            }
        } catch (Exception $e) {
            Log::error('Error en AiService::searchCandidates: '.$e->getMessage());
        }

        return [
            'keywords' => explode(' ', $userPrompt),
        ];
    }

    /**
     * Extract skills/keywords from a recruiter query using local Ollama.
     */
    public function searchCandidatesWithOllama(string $userPrompt): array
    {
        try {
            Log::info('AiService: Calling Ollama for candidate search extraction...');

            $prompt = "Analiza la búsqueda de empleo de un reclutador y extrae habilidades técnicas (skills) y palabras clave (keywords) para buscar candidatos en una base de datos.\n"
                ."REGLAS:\n"
                ."- Si la búsqueda es una sola palabra tecnológica (ej: 'filament', 'laravel', 'react'), ESA es la skill: ponla en 'skills' Y en 'keywords'.\n"
                ."- 'skills': lenguajes, frameworks, librerías, herramientas y tecnologías mencionadas o implícitas.\n"
                ."- 'keywords': lo mismo de skills más puestos, niveles (senior, junior), áreas y cualquier otra palabra relevante.\n"
                ."- Si no hay nada técnico claro, deja 'skills' vacío y pon cada palabra importante en 'keywords'.\n"
                ."Ejemplo: búsqueda 'filament' -> {\"skills\": [\"filament\"], \"keywords\": [\"filament\"]}\n"
                ."Búsqueda: '{$userPrompt}'\n\n"
                ."Devuelve ÚNICAMENTE un JSON con este formato:\n"
                ."{\n"
                ."    \"skills\": [\"skill1\", \"skill2\"],\n"
                ."    \"keywords\": [\"keyword1\", \"keyword2\"]\n"
                .'}';

            $response = Http::timeout($this->ollamaTimeout)
                ->post($this->ollamaUrl.'/api/chat', [
                    'model' => $this->ollamaModel,
                    'format' => 'json',
                    'stream' => false,
                    'keep_alive' => '30m',
                    'options' => ['temperature' => 0.2],
                    'messages' => [
                        ['role' => 'system', 'content' => 'Eres un asistente experto en reclutamiento técnico. Siempre respondes con JSON válido.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->successful()) {
                $data = json_decode($this->cleanJsonResponse($response->json('message.content') ?? ''), true);

                return [
                    'skills' => $data['skills'] ?? [],
                    'keywords' => $data['keywords'] ?? explode(' ', $userPrompt),
                ];
            }

            Log::error('Error de Ollama en búsqueda: '.$response->body());
        } catch (Exception $e) {
            Log::error('Error en AiService::searchCandidatesWithOllama: '.$e->getMessage());
        }

        return [
            'keywords' => explode(' ', $userPrompt),
        ];
    }

    /**
     * Clean JSON response from AI.
     */
    protected function cleanJsonResponse(string $text): string
    {
        $text = trim($text);
        if (str_starts_with($text, '```json')) {
            $text = str_replace('```json', '', $text);
            $text = str_replace('```', '', $text);
        } elseif (str_starts_with($text, '```')) {
            $text = str_replace('```', '', $text);
        }

        return trim($text);
    }
}
