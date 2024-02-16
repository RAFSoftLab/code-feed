<?php

namespace App\Http\Controllers\GitHub;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
            // Redirect
            return redirect('/github/auth-success?access_token=' . $accessToken . '&refresh_token=' . $refreshToken);
        }
        if ($response->clientError()) {
            echo 'Error: ' . $response->body();
        }

        if ($response->serverError()) {
            echo 'Error: ' . $response->body();
        }
    }
}
