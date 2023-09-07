<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // validasi
        $attr = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // create user
        $user = User::create([
            'name' => $attr['name'],
            'email' => $attr['email'],
            'password' => bcrypt($attr['password']),
        ]);

        // return 
        return response([
            'user' => $user,
            'token' => $user->createToken('scret')->plainTextToken
        ], 200);
    }

    public function login(Request $request)
    {
        // validasi
        $attr = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // attempt login
        if (!Auth::attempt($attr)) {
            return response([
                'message' => 'Invalid Credential..!',
            ], 403);
        }

        // return 
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('scret')->plainTextToken
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Logout Success..',
        ], 200);
    }

    public function user()
    {
        return response([
            'user' => auth()->user(),
        ], 200);
    }
}
