<?php

namespace App\Services;

use App\Models\Candidate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.google_ai.key') ?? env('GOOGLE_AI_API_KEY', '');
    }

    /**
     * Analyze a candidate's profile and return a rating (0-100) and summary.
     */
    public function analyzeCandidate(Candidate $candidate): array
    {
        if (empty($this->apiKey)) {
            return [
                'rating' => 0,
                'summary' => 'API Key de Google AI no configurada.',
            ];
        }

        $profileData = [
            'summary' => $candidate->summary,
            'work_experiences' => $candidate->workExperiences->map(fn($exp) => [
                'company' => $exp->company_name,
                'title' => $exp->job_title,
                'duration' => $exp->start_date . ' - ' . ($exp->end_date ?? 'Actualidad'),
            ]),
            'skills' => $candidate->skills->pluck('name'),
            'education' => $candidate->educations->map(fn($edu) => [
                'institution' => $edu->institution,
                'degree' => $edu->degree,
            ]),
        ];

        $prompt = "Eres un reclutador experto. Analiza el siguiente perfil de candidato y devuelve un objeto JSON con:
        1. 'rating': Un número del 0 al 100 basado en su experiencia y habilidades.
        2. 'summary': Un resumen profesional de 3 líneas destacando sus fortalezas.
        
        Perfil del candidato: " . json_encode($profileData) . "
        
        Devuelve SOLO el JSON, sin etiquetas de código ni explicaciones adicionales.";

        try {
            $response = Http::post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                $data = json_decode($this->cleanJsonResponse($text), true);

                return [
                    'rating' => $data['rating'] ?? 0,
                    'summary' => $data['summary'] ?? 'Error al procesar la respuesta de la IA.',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error en AiService::analyzeCandidate: ' . $e->getMessage());
        }

        return [
            'rating' => 0,
            'summary' => 'No se pudo completar el análisis en este momento.',
        ];
    }

    /**
     * Convert a natural language prompt into search filters for candidates.
     */
    public function searchCandidates(string $userPrompt): array
    {
        if (empty($this->apiKey)) {
            return [];
        }

        $prompt = "Eres un asistente de búsqueda de talento. El usuario busca: \"$userPrompt\".
        Traduce esta búsqueda a un objeto JSON con criterios de filtrado:
        1. 'skills': Array de habilidades clave mencionadas o implícitas.
        2. 'keywords': Palabras clave para buscar en el resumen profesional.
        3. 'min_experience_years': Número de años de experiencia sugeridos (opcional).
        
        Devuelve SOLO el JSON.";

        try {
            $response = Http::post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                return json_decode($this->cleanJsonResponse($text), true) ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Error en AiService::searchCandidates: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Clean JSON response from AI in case it includes markdown code blocks.
     */
    protected function cleanJsonResponse(string $text): string
    {
        $text = trim($text);
        if (str_starts_with($text, '```json')) {
            $text = substr($text, 7);
        }
        if (str_ends_with($text, '```')) {
            $text = substr($text, 0, -3);
        }
        return trim($text);
    }
}
