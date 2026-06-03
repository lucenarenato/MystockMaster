<?php

namespace App\Http\Controllers\Api;

use App\Actions\ProvisionTenant;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class AuthController extends Controller
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'company'  => 'required|string|max:255',
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = DB::transaction(function () use ($validatedData) {
                $user = User::create([
                    'name'     => $validatedData['name'],
                    'email'    => $validatedData['email'],
                    'password' => Hash::make($validatedData['password']),
                ]);

                app(ProvisionTenant::class)->handle($user, $validatedData['company']);

                return $user;
            });

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ], 422);
        }
    }
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        // dd($request);
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}