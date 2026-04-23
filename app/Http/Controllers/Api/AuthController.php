<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $token = $customer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'customer' => [
                'id'             => $customer->id,
                'name'           => $customer->name,
                'email'          => $customer->email,
                'phone'          => $customer->phone,
                'loyalty_points' => $customer->loyalty_points,
                'photo'          => $customer->photo ? asset('storage/' . $customer->photo) : null,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user('sanctum')->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    public function me(Request $request): JsonResponse
    {
        $customer = $request->user('sanctum');

        return response()->json([
            'id'             => $customer->id,
            'name'           => $customer->name,
            'email'          => $customer->email,
            'phone'          => $customer->phone,
            'loyalty_points' => $customer->loyalty_points,
            'photo'          => $customer->photo ? asset('storage/' . $customer->photo) : null,
        ]);
    }
}
