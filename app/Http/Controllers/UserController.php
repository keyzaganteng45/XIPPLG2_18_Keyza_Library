<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'required|string',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
            ]);
            $token = $user->createToken('UserToken')->plainTextToken;
            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $data = $request->only('email', 'password');
            if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $user = Auth::user();
            $token = $user->createToken('UserToken')->plainTextToken;
            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ], 200);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        try {
            $users = User::all();
            return response()->json(UserResource::collection($users), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user()->id;
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,',
                'phone' => 'sometimes|required|string',
            ]);
            $user->update($validated);
            return response()->json(new UserResource($user), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
