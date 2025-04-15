<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return $this->sendResponse(
        true,
        [
            'user' => new UserResource($user),
            'access_token' => $token,
        ],
        'Login successful', 200);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->except(['password_confirmation']));

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }
}
