<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CognitoService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $cognito;

    public function __construct(CognitoService $cognito)
    {
        $this->cognito = $cognito;
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'nullable|string|max:255',
        ]);

        $response = $this->cognito->registerUser(
            $request->email,
            $request->password,
            $request->name
        );


        if (!$response) {
            return response()->json(['error' => 'error registering user'], 400);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'UserConfirmed' => $response['UserConfirmed'] ?? NULL,
            'UserSub' => $response['UserSub'] ?? NULL,
            'Session' => $response['Session'] ?? NULL,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $authResult = $this->cognito->authenticateUser($request->email, $request->password);

        if (is_string($authResult)) {
            return response()->json(['error' => $authResult], 401);
        }

        return response()->json($authResult);
    }
}
