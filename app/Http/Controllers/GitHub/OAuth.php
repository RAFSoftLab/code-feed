<?php

namespace App\Http\Controllers\GitHub;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class OAuth extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Get the code query parameter and make a request to the GitHub oauth api.
        $code = $request->query('code');

        // Make a request to the GitHub oauth api
        $response = Http::post('https://github.com/login/oauth/access_token', [
            'client_id' => config('services.github.client_id'),
            'client_secret' => config('services.github.client_secret'),
            'code' => $code,
        ]);

        if ($response->successful()) {
            parse_str($response->body(), $parsed);
            $accessToken = $parsed['access_token'];
            $refreshToken = $parsed['refresh_token'];
            $refreshTokenExpiresAt = now()->addSeconds($parsed['expires_in']);
            $this->updateOrCreateUser($accessToken, $refreshToken, $refreshTokenExpiresAt);

            return redirect(route('dashboard'));
        }

        if ($response->clientError()) {
            echo 'Error: ' . $response->body();
        }

        if ($response->serverError()) {
            echo 'Error: ' . $response->body();
        }
    }

    private function updateOrCreateUser(string $accessToken, string $refreshToken, $refreshTokenExpiresAt)
    {
        // Save tokens to user fields.
        if (Auth::check()) {
            $user = Auth::user();
            $user->github_access_token = $accessToken;
            $user->github_refresh_token = $refreshToken;
            $user->update();
        } else {
            $response = Http::withToken($accessToken)->get('https://api.github.com/user')->json();
            $user = User::firstOrCreate([
                'email' => $response['email'],
            ])->fill([
                'name' => $response['login'],
                'github_id' => $response['login'],
                'github_access_token' => $accessToken,
                'github_refresh_token' => $refreshToken,
                'github_refresh_token_expires_at' => $refreshTokenExpiresAt,
            ]);
            $user->update();

            Auth::login($user);
            Session::regenerate();
        }
    }
}
