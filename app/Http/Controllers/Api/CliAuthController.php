<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceAuthorization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CliAuthController extends Controller
{
    /**
     * LocalJudge / CLI requests authorization.
     */
    public function request(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => ['nullable', 'string', 'max:100'],
            'callback_port' => ['nullable', 'integer', 'between:1024,65535'],
        ]);

        $clientName = $validated['client_name'] ?? 'LocalJudge';
        $callbackPort = $validated['callback_port'] ?? null;

        $authRequest = DeviceAuthorization::createRequest($clientName, $callbackPort);

        $query = [
            'code' => $authRequest->user_code,
            'device_code' => $authRequest->device_code,
        ];
        if ($authRequest->callback_port) {
            $query['port'] = $authRequest->callback_port;
        }

        $verificationUrl = url('/cli/authorize?'.http_build_query($query));

        return response()->json([
            'status' => 'pending',
            'device_code' => $authRequest->device_code,
            'user_code' => $authRequest->user_code,
            'verification_url' => $verificationUrl,
            'expires_in' => 900,
            'interval' => 2,
        ], 200);
    }

    /**
     * LocalJudge / CLI polls for authorization result.
     */
    public function poll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_code' => ['required', 'string'],
        ]);

        $authRequest = DeviceAuthorization::query()
            ->with('user')
            ->where('device_code', $validated['device_code'])
            ->first();

        if (! $authRequest || $authRequest->isExpired()) {
            return response()->json([
                'status' => 'expired',
                'message' => 'Authorization request has expired or was not found.',
            ], 410);
        }

        if ($authRequest->status === 'denied') {
            return response()->json([
                'status' => 'denied',
                'message' => 'User denied the authorization request in the browser.',
            ], 403);
        }

        if ($authRequest->status === 'pending') {
            return response()->json([
                'status' => 'pending',
                'message' => 'Waiting for user authorization in browser...',
                'interval' => 2,
            ], 200);
        }

        if ($authRequest->status === 'approved') {
            $user = $authRequest->user;

            return response()->json([
                'status' => 'approved',
                'token' => $authRequest->token,
                'user' => [
                    'id' => $user?->id,
                    'name' => $user?->name,
                    'username' => $user?->username,
                    'email' => $user?->email,
                ],
                'message' => 'LocalJudge authorization successful.',
            ], 200);
        }

        return response()->json([
            'status' => 'unknown',
            'message' => 'Unexpected authorization state.',
        ], 400);
    }

    /**
     * Verify active token authentication for LocalJudge.
     */
    public function whoami(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'authenticated' => false,
                'message' => 'Unauthenticated token.',
            ], 401);
        }

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ],
        ]);
    }
}
