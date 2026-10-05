<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramNotifier
{
    /**
     * Send a message through the Telegram Bot API. Never throws.
     */
    public function send(string $htmlMessage): bool
    {
        $token = (string) config('services.telegram.token');
        $chatId = (string) config('services.telegram.chat_id');

        if ($token === '' || $chatId === '') {
            Log::warning('TelegramNotifier: TELEGRAM_BOT_TOKEN o TELEGRAM_CHAT_ID sin configurar.');

            return false;
        }

        try {
            $response = Http::timeout(15)->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $htmlMessage,
                    'parse_mode' => 'HTML',
                ]
            );

            if (! $response->successful() || ! $response->json('ok', false)) {
                Log::warning('TelegramNotifier: la API respondió con error: '.$response->body());

                return false;
            }

            Log::info('TelegramNotifier: aviso enviado correctamente.');

            return true;
        } catch (Throwable $e) {
            Log::warning('TelegramNotifier: no se pudo contactar a Telegram: '.$e->getMessage());

            return false;
        }
    }
}
