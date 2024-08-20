<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request)
    {
        // Ambil data pengguna yang sedang login
        $user = auth()->user();

        // Validasi input dari pengguna
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|confirmed',
        ]);

        // Perbarui data pengguna
        $user->username = $request->input('username');
        $user->email = $request->input('email');

        // Periksa apakah password diisi, jika iya, lakukan update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Simpan perubahan
        $user->save();

        // Kembalikan respons sukses
        return response()->json(['message' => 'User updated successfully'], 200);
    }
}