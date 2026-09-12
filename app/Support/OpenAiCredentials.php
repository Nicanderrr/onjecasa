<?php

namespace App\Support;

class OpenAiCredentials
{
    public static function apiKey(): ?string
    {
        $key = self::readEnvValue('OPENAI_API_KEY');

        if (! $key) {
            $key = config('services.openai.api_key');
        }

        $key = trim((string) $key);

        return $key !== '' ? $key : null;
    }

    public static function model(string $name = 'OPENAI_MODEL', string $default = 'gpt-4o-mini'): string
    {
        return self::readEnvValue($name) ?: env($name, $default);
    }

    public static function baseUrl(): string
    {
        return rtrim(self::readEnvValue('OPENAI_BASE_URL') ?: env('OPENAI_BASE_URL', 'https://api.openai.com/v1'), '/');
    }

    public static function realtimeModel(): string
    {
        return self::model('OPENAI_REALTIME_MODEL', 'gpt-realtime-2.1');
    }

    public static function realtimeVoice(): string
    {
        return self::model('OPENAI_REALTIME_VOICE', 'marin');
    }

    public static function transcribeModel(): string
    {
        return self::model('OPENAI_TRANSCRIBE_MODEL', 'gpt-4o-mini-transcribe');
    }

    private static function readEnvValue(string $name): ?string
    {
        $path = base_path('.env');

        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        if (! preg_match('/^' . preg_quote($name, '/') . '=(.*)$/m', $contents, $matches)) {
            return null;
        }

        $value = trim((string) $matches[1]);
        $value = trim($value, "\"'");
        $value = preg_replace('/\s+/', '', $value);

        return $value !== '' ? $value : null;
    }
}
