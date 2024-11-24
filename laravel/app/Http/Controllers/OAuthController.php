<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    public function redirect()
    {
        // Redirect the user to the Go OAuth2 authorization endpoint
        $authorizationUrl = env('OAUTH_SERVER_URL') . '/oauth/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => env('OAUTH_CLIENT_ID'),
                'redirect_uri' => env('OAUTH_REDIRECT_URI'),
                'state' => 'xyz',  // Optional state parameter for security
            ]);
        return redirect($authorizationUrl);
    }

    public function callback(Request $request)
    {
        $client = new Client();
        $tokenUrl = env('OAUTH_SERVER_URL') . '/oauth/token';

        try {
            // Set up the request with client credentials in the Authorization header
            $response = $client->post($tokenUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(env('OAUTH_CLIENT_ID') . ':' . env('OAUTH_CLIENT_SECRET')),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => env('OAUTH_REDIRECT_URI'),
                    'code' => $request->input('code'),
                ],
            ]);

            $tokenData = json_decode($response->getBody()->getContents(), true);

            // Access additional user information (e.g., email and username)
            return response()->json([
                'status' => 'ok',
                'access_token' => $tokenData['access_token'] ?? null,
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_in' => $tokenData['expires_in'] ?? null,
                'email' => $tokenData['email'] ?? null,  // Capture email from the token response
                'username' => $tokenData['username'] ?? null, // Capture username from the token response
            ]);

        } catch (\Exception $e) {
            \Log::error('OAuth2 Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



}
