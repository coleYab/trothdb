<?php

namespace App\Http\Controllers;

use App\Models\CliToken;
use App\Models\DeviceAuthorization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CliAuthorizeController extends Controller
{
    /**
     * Show the authorization page where user grants permission to LocalJudge.
     */
    public function show(Request $request): Response
    {
        $code = $request->query('code');
        $deviceCode = $request->query('device_code');
        $port = $request->query('port');

        $authRequest = null;
        if ($code || $deviceCode) {
            $authRequest = DeviceAuthorization::query()
                ->where(function ($q) use ($code, $deviceCode) {
                    if ($code) {
                        $q->where('user_code', $code);
                    }
                    if ($deviceCode) {
                        $q->orWhere('device_code', $deviceCode);
                    }
                })
                ->first();
        }

        $user = $request->user();

        return Inertia::render('cli/authorize', [
            'authRequest' => $authRequest ? [
                'user_code' => $authRequest->user_code,
                'device_code' => $authRequest->device_code,
                'client_name' => $authRequest->client_name,
                'callback_port' => $authRequest->callback_port ?? ($port ? (int) $port : null),
                'status' => $authRequest->isExpired() ? 'expired' : $authRequest->status,
                'expires_at' => $authRequest->expires_at->toIso8601String(),
            ] : null,
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * User approves the authorization request.
     */
    public function approve(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'device_code' => ['required', 'string'],
        ]);

        $authRequest = DeviceAuthorization::query()
            ->where('device_code', $validated['device_code'])
            ->first();

        if (! $authRequest || $authRequest->isExpired()) {
            return response()->json([
                'status' => 'expired',
                'message' => 'Authorization request has expired.',
            ], 410);
        }

        $user = $request->user();

        // Generate persistent CLI token
        $cliToken = CliToken::generate($user, $authRequest->client_name.' Session');

        $authRequest->update([
            'user_id' => $user->id,
            'token' => $cliToken->token,
            'status' => 'approved',
        ]);

        $callbackUrl = null;
        if ($authRequest->callback_port) {
            $callbackUrl = "http://127.0.0.1:{$authRequest->callback_port}/callback?".http_build_query([
                'token' => $cliToken->token,
                'user' => $user->username ?? $user->name,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'approved',
                'token' => $cliToken->token,
                'callback_url' => $callbackUrl,
                'message' => 'Authorization granted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Connected successfully.');
    }

    /**
     * User denies the authorization request.
     */
    public function deny(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'device_code' => ['required', 'string'],
        ]);

        $authRequest = DeviceAuthorization::query()
            ->where('device_code', $validated['device_code'])
            ->first();

        if ($authRequest) {
            $authRequest->update(['status' => 'denied']);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'denied',
                'message' => 'Authorization request denied.',
            ]);
        }

        return redirect()->back();
    }
}
