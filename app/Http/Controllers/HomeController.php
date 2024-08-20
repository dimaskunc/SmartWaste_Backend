<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index() {
        $user = User::all();

        return response()->json($user, 200);
    }

    public function register(Request $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'User',
        ]);

        if($user){
            http_response_code(201);
            return response()->json(['message' => 'succesfuly']);
        } else {
            http_response_code(400);
            return response()->json(['message' => 'gagal']);
        }
    }

    public function login(Request $request){
        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('kode_rahassia')->accessToken;
            $token = $user->createToken('kode_rahassia')->plainTextToken;

            return response()->json(['token' => $token, 'role'=> $user->role], 200);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function update(Request $request) {
        $user = Auth::user();

        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string',
        ]);

        // Update user details
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully'], 200);
    }
}
