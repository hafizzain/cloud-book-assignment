<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:author,collaborator',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

       $role = Role::where('name', $request->role)->first();
       if($role)
       {
         $user->assignRole($role);
       }

       $token = $user->createToken('auth_token')->plainTextToken;

       return response()->json(['user' => $user, 'token' => $token, 'status' => 201], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials))
        {
            return response()->json(['message' => 'Invalid credentials.', 'status' => 401], 401);
        }

        $user = User::find(Auth::id());
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Login successfully.', 'token' => $token, 'status' => 200], 200);
    }

    public function logout(Request $request)
    {
        $accessToken = $request->bearerToken();
        $token = PersonalAccessToken::findToken($accessToken);
        
        if (!empty($token)) {
            $token = $token->delete();
        }

       return response()->json(['message' => 'Logged out successfully.', 'status' => 200], 200);
    }

    public function me()
    {
        return response()->json(['message' => 'success', 'user' => auth()->user(), 'role' => auth()->user()->roles->first()->name ?? 'No role assigned', 'status' => 200], 200);
    }
}
