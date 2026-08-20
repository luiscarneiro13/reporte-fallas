<?php

namespace App\Services;

use App\Models\PushToken;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Capa de transporte hacia la Expo Push API (https://exp.host/--/api/v2/push/send).
 * No usa ningún SDK de Firebase/FCM: Expo abstrae APNs/FCM internamente a partir de
 * tokens tipo "ExponentPushToken[...]" (ver docs/api-endpoints-spec.md §1 y §6.4).
 * Nunca lanza excepciones hacia el llamador: cualquier fallo se loguea y se absorbe,
 * para que un problema de push no rompa el flujo de negocio que lo dispara.
 */
class ExpoPushService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    public function __construct(private ?Client $client = null)
    {
        $this->client = $this->client ?: new Client(['timeout' => 10]);
    }

    public function sendToUser(int $userId, array $payload): array
    {
        return $this->sendToUsers([$userId], $payload);
    }

    public function sendToUsers(array $userIds, array $payload): array
    {
        $tokens = PushToken::query()
            ->active()
            ->whereIn('user_id', $userIds)
            ->pluck('token')
            ->all();

        return $this->sendToTokens($tokens, $payload);
    }

    public function sendToTokens(array $tokens, array $payload): array
    {
        $tokens = array_values(array_unique(array_filter($tokens)));

        $summary = ['sent' => 0, 'failed' => 0, 'invalid_tokens_removed' => 0];

        foreach ($tokens as $token) {
            $result = $this->sendToToken($token, $payload);

            if ($result['sent']) {
                $summary['sent']++;
            } else {
                $summary['failed']++;
            }

            if ($result['invalid_token_removed']) {
                $summary['invalid_tokens_removed']++;
            }
        }

        return $summary;
    }

    public function sendToToken(string $token, array $payload): array
    {
        $result = ['sent' => false, 'invalid_token_removed' => false];

        try {
            $response = $this->client->post(self::EXPO_PUSH_URL, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $this->buildMessage($token, $payload),
            ]);

            $body = json_decode((string) $response->getBody(), true);
            $ticket = $body['data'] ?? null;

            if (($ticket['status'] ?? null) === 'error') {
                $errorType = $ticket['details']['error'] ?? null;

                Log::warning('ExpoPushService: ticket de error de Expo', [
                    'token' => $token,
                    'message' => $ticket['message'] ?? null,
                    'error_type' => $errorType,
                ]);

                if ($errorType === 'DeviceNotRegistered') {
                    $this->removeInvalidToken($token);
                    $result['invalid_token_removed'] = true;
                }

                return $result;
            }

            PushToken::query()->where('token', $token)->update(['last_used_at' => now()]);
            $result['sent'] = true;
        } catch (\Throwable $e) {
            Log::error('ExpoPushService: fallo al enviar push', [
                'token' => $token,
                'message' => $e->getMessage(),
            ]);
        }

        return $result;
    }

    private function removeInvalidToken(string $token): void
    {
        PushToken::query()->where('token', $token)->forceDelete();
    }

    private function buildMessage(string $token, array $payload): array
    {
        $message = [
            'to' => $token,
            'sound' => 'default',
            'title' => $payload['title'] ?? null,
            'body' => $payload['body'] ?? null,
        ];

        $data = array_diff_key($payload, array_flip(['title', 'body', 'largeIcon', 'bigPicture']));

        if (!empty($data)) {
            $message['data'] = $data;
        }

        return $message;
    }
}
