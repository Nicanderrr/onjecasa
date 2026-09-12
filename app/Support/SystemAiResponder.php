<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SystemAiResponder
{
    public function chat(string $context, string $message, array $history = [], string $logLabel = 'System AI')
    {
        if (trim($message) === '') {
            return response()->json(['reply' => 'Please type a message.'], 422);
        }

        $apiKey = OpenAiCredentials::apiKey();
        $model = OpenAiCredentials::model();

        if (! $apiKey) {
            return response()->json(['reply' => 'OpenAI API key is not configured. Add OPENAI_API_KEY in .env.']);
        }

        try {
            $messages = array_merge([
                ['role' => 'system', 'content' => $context],
            ], $this->cleanHistory($history));

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                ]);

            if ($response->successful()) {
                $rawReply = (string) data_get($response->json(), 'choices.0.message.content', 'No response generated.');

                return response()->json([
                    'reply' => $this->normalizePlainReply($rawReply),
                ]);
            }

            Log::error($logLabel . ' OpenAI API error', ['status' => $response->status(), 'body' => $response->body()]);
            $providerMessage = (string) data_get($response->json(), 'error.message', '');
            $providerCode = (string) data_get($response->json(), 'error.code', '');
            $details = trim($providerMessage . ($providerCode !== '' ? ' (' . $providerCode . ')' : ''));

            return response()->json([
                'error' => $details !== '' ? $details : ('OpenAI API error: ' . $response->status()),
            ], 500);
        } catch (\Throwable $e) {
            Log::error($logLabel . ' exception', ['message' => $e->getMessage()]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function cleanHistory(array $history): array
    {
        return collect($history)
            ->filter(fn ($item) => is_array($item) && in_array($item['role'] ?? null, ['user', 'assistant'], true) && isset($item['content']))
            ->take(-12)
            ->map(fn ($item) => [
                'role' => $item['role'],
                'content' => mb_substr((string) $item['content'], 0, 2000),
            ])
            ->values()
            ->all();
    }

    private function normalizePlainReply(string $reply): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $reply);
        $text = preg_replace('/[`*_#>~]+/u', '', $text);
        $text = preg_replace('/^\s*[-•]\s*/mu', '', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $text = preg_replace('/[ \t]{2,}/', ' ', $text);

        return trim((string) $text);
    }
}
