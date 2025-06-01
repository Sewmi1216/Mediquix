<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request as HttpRequest;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users',
            'password'       => 'required|string|min:6',
            'contact_number' => 'required|string',
            'dob'            => 'required|date',
            'address'        => 'required|string',
            'user_type'      => 'required|string'
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'address'        => $request->address,
            'dob'            => $request->dob,
            'contact_number' => $request->contact_number,
            'user_type'      => $request->user_type ?? 'user',
        ]);

        return response()->json(['message' => 'User registered successfully']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $tokenRequest = HttpRequest::create('/oauth/token', 'POST', [
            'grant_type'    => 'password',
            'client_id'     => env('PASSPORT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'username'      => $request->email,
            'password'      => $request->password,
            'scope'         => '',
        ]);

        $response = app()->handle($tokenRequest);

        if ($response->getStatusCode() === 200) {
            $tokenData = json_decode($response->getContent(), true);
            $user = User::where('email', $request->email)->first();

            return response()->json([
                'access_token'  => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'token_type'    => $tokenData['token_type'],
                'expires_in'    => $tokenData['expires_in'],
                'user'          => [
                    'id'        => $user->id,
                    'email'     => $user->email,
                    'user_type' => $user->user_type
                ]
            ]);
        }

        $responseData = json_decode($response->getContent(), true);

        return response()->json([
            'error'   => 'Unauthorized',
            'details' => $responseData
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
