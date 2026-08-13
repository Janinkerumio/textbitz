<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class BroadcastAuthProxyController extends Controller
{
    public function authorize(Request $request)
    {
        $user = $request->user();
        $token = $user?->remote_token;

        if(!$token)
        {
            return response()->json(['message' => 'No remote session'], 401);
        }

        $request->validate([
            'channel_name' => 'required|string',
            'socket_id' => 'required|string',
        ]);

        $url = rtrim(config('services.textbitz.server_url'), '/') . '/api/broadcasting/auth';

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->asForm()
                ->post($url, [
                    'channel_name' => $request->input('channel_name'),
                    'socket_id' => $request->input('socket_id'),
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Broadcast auth proxy: remote unreachable', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Remote server unreachable'], 503);
        }

        if ($response->status() === 401) {
            $user->update(['remote_token' => null]);
        }

        return response($response->body(), $response->status())
            ->header('Content-Type', 'application/json');
    }
}
