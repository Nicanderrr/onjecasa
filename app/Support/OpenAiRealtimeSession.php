<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiRealtimeSession
{
    public function connect(string $sdp, string $instructions, string $logLabel = 'Realtime AI')
    {
        if (! str_contains($sdp, 'v=0')) {
            return response('Missing SDP offer.', 422);
        }

        $apiKey = OpenAiCredentials::apiKey();

        if (! $apiKey) {
            return response('OpenAI API key is not configured.', 500);
        }

        $session = [
            'type' => 'realtime',
            'model' => OpenAiCredentials::realtimeModel(),
            'instructions' => $instructions,
            'output_modalities' => ['audio'],
            'audio' => [
                'input' => [
                    'noise_reduction' => [
                        'type' => 'far_field',
                    ],
                    'transcription' => [
                        'model' => OpenAiCredentials::transcribeModel(),
                        'prompt' => 'The user is speaking naturally in a POS system. Product names, receipt numbers, payment methods, Ghanaian names, and occasional local phrasing may occur.',
                    ],
                    'turn_detection' => [
                        'type' => 'server_vad',
                        'threshold' => 0.35,
                        'prefix_padding_ms' => 300,
                        'silence_duration_ms' => 550,
                        'create_response' => true,
                        'interrupt_response' => true,
                    ],
                ],
                'output' => [
                    'voice' => OpenAiCredentials::realtimeVoice(),
                ],
            ],
        ];

        try {
            $response = Http::withToken($apiKey)
                ->baseUrl(OpenAiCredentials::baseUrl())
                ->timeout(60)
                ->asMultipart()
                ->post('/realtime/calls', [
                    [
                        'name' => 'sdp',
                        'contents' => $sdp,
                    ],
                    [
                        'name' => 'session',
                        'contents' => json_encode($session),
                    ],
                ])
                ->throw();
        } catch (\Throwable $exception) {
            Log::warning($logLabel . ' session failed.', [
                'message' => $exception->getMessage(),
                'model' => OpenAiCredentials::realtimeModel(),
            ]);

            return response('Realtime session failed: ' . $exception->getMessage(), 502);
        }

        return response($response->body(), 200, [
            'Content-Type' => 'application/sdp',
            'Cache-Control' => 'no-store',
        ]);
    }
}
