<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\PushTokenDestroyRequest;
use App\Http\Requests\Api\V1\Mobile\PushTokenStoreRequest;
use App\Models\PushToken;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\Request;

/**
 * Spec §6.4. El user_id se toma SIEMPRE de $request->user()->id (Sanctum),
 * nunca del body.
 */
class PushTokenController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $tokens = PushToken::query()
            ->active()
            ->forUser($request->user()->id)
            ->orderByDesc('last_used_at')
            ->get(['id', 'platform', 'device_id', 'app_version', 'last_used_at', 'created_at']);

        return $this->success(['data' => $tokens]);
    }

    public function store(PushTokenStoreRequest $request)
    {
        $userId = $request->user()->id;
        $deviceId = $request->input('device_id');

        if ($deviceId) {
            PushToken::query()
                ->active()
                ->where('device_id', $deviceId)
                ->where('user_id', '!=', $userId)
                ->delete();
        }

        $token = PushToken::withTrashed()->firstOrNew(['token' => $request->input('token')]);
        $wasTrashed = $token->trashed();

        $token->fill([
            'user_id' => $userId,
            'platform' => $request->input('platform'),
            'device_id' => $deviceId,
            'app_version' => $request->input('app_version'),
            'last_used_at' => now(),
        ]);

        $wasTrashed ? $token->restore() : $token->save();

        return $this->success(['token_registered' => true], 'Push token registered');
    }

    public function destroy(PushTokenDestroyRequest $request)
    {
        PushToken::query()
            ->active()
            ->forUser($request->user()->id)
            ->where('token', $request->input('token'))
            ->delete();

        return $this->success(null, 'Push token removed');
    }
}
