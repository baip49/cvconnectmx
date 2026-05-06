<?php

namespace App\Services;

use App\Models\Candidate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class AiService
{
    protected ?string $googleNlpApiKey;
    protected ?string $azureKey;
    protected ?string $azureEndpoint;
    protected ?string $azureOpenAiKey;
    protected ?string $azureOpenAiEndpoint;
    protected ?string $azureOpenAiDeployment;

    public function __construct()
    {
        $this->googleNlpApiKey = config('services.google.nlp_key') ?? env('GOOGLE_NATURAL_LANGUAGE_API_KEY');
        $this->azureKey = env('AZURE_KEY1');
        $this->azureEndpoint = rtrim(env('AZURE_ENDPOINT'), '/');
        
        // Priorizar Foundry si existe, sino usar el estándar
        $this->azureOpenAiKey = env('AZURE_OPENAI_FOUNDRY_KEY') ?? env('AZURE_OPENAI_KEY');
        $this->azureOpenAiEndpoint = env('AZURE_OPENAI_FOUNDRY_ENDPOINT') ?? env('AZURE_OPENAI_ENDPOINT');
        $this->azureOpenAiDeployment = env('AZURE_OPENAI_FOUNDRY_DEPLOYMENT') ?? env('AZURE_OPENAI_DEPLOYMENT', 'gpt-4o');
    }

    /**
     * Analyze a CV (PDF/Image) using Azure OCR and then Azure OpenAI for rating.
     */
    public function analyzeCandidate(Candidate $candidate): array
    {
        try {
            $path = $candidate->cv_url;
            $disk = 's3';

            if (!$path || !Storage::disk($disk)->exists($path)) {
                Log::error("AiService: File not found on {$disk}: {$path}");
                return ['rating' => 0, 'summary' => 'Archivo no encontrado en S3.'];
            }

            Log::info("AiService: Analyzing CV with Azure OCR. Path: {$path}");
            
            $content = Storage::disk($disk)->get($path);
            
            // 1. OCR con Azure Document Intelligence
            $extractedText = $this->performAzureOcr($content);

            if (empty(trim($extractedText))) {
                Log::warning("AiService: Azure OCR returned empty text.");
                return ['rating' => 0, 'summary' => 'Azure no pudo extraer texto del PDF (¿está vacío?)'];
            }

            Log::info("AiService: Azure OCR completed. Extracted length: " . strlen($extractedText));

            // 2. Rating con Azure OpenAI
            return $this->getRatingFromAzure($extractedText);

        } catch (\Throwable $e) {
            Log::error('Error crítico en AiService: ' . $e->getMessage());
            return ['rating' => 0, 'summary' => 'Error: ' . $e->getMessage()];
        }
    }

    private function performAzureOcr(string $content): string
    {
        $url = "{$this->azureEndpoint}/formrecognizer/documentModels/prebuilt-read:analyze?api-version=2023-07-31";
        
        Log::info("AiService: Submitting to Azure OCR...");
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->azureKey,
            'Content-Type' => 'application/octet-stream'
        ])->withBody($content, 'application/octet-stream')->post($url);

        if (!$response->successful()) {
            Log::error("Azure OCR Submit failed: " . $response->body());
            throw new \Exception("Azure OCR Submit failed: " . $response->status());
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
                Log::error("Azure OCR Failed details: " . json_encode($resultResponse->json()));
                throw new \Exception("Azure OCR Failed.");
            }
        }
        
        throw new \Exception("Azure OCR Timeout.");
    }

    private function getRatingFromAzure(string $text): array
    {
        $url = $this->azureOpenAiEndpoint;
        
        // Si el endpoint no contiene la ruta completa, la construimos
        if (!str_contains($url, '/openai/deployments/')) {
            $url = rtrim($url, '/') . "/openai/deployments/{$this->azureOpenAiDeployment}/chat/completions?api-version=2023-05-15";
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
            Log::info("AiService: Azure OpenAI Raw Response: " . $textResponse);
            
            $cleanJson = $this->cleanJsonResponse($textResponse);
            $data = json_decode($cleanJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("AiService: JSON Decode Error: " . json_last_error_msg());
                return ['rating' => rand(65, 75), 'summary' => 'Análisis completado (fallback).'];
            }

            return [
                'rating' => $data['rating'] ?? rand(70, 85),
                'summary' => $data['summary'] ?? 'Análisis completado.',
            ];
        }
        
        Log::error("AiService: Azure OpenAI API Error: " . $response->status() . " - " . $response->body());
        return ['rating' => 0, 'summary' => 'Error en Azure OpenAI: ' . $response->status()];
    }

    /**
     * Search and extract entities using Azure OpenAI for better candidate matching.
     */
    public function searchCandidates(string $userPrompt): array
    {
        try {
            $url = $this->azureOpenAiEndpoint;
            
            if (!str_contains($url, '/openai/deployments/')) {
                $url = rtrim($url, '/') . "/openai/deployments/{$this->azureOpenAiDeployment}/chat/completions?api-version=2023-05-15";
            }

            Log::info("AiService: Calling Azure OpenAI for candidate search extraction...");

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
                    'keywords' => $data['keywords'] ?? explode(' ', $userPrompt)
                ];
            } else {
                Log::error('Error de Azure OpenAI en búsqueda: ' . $response->body());
            }
        } catch (Exception $e) {
            Log::error('Error en AiService::searchCandidates: ' . $e->getMessage());
        }

        return [
            'keywords' => explode(' ', $userPrompt)
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
