<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UserController extends Controller

{

    public function register(Request $request)
    {
        //validate fields
        $attrs = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'dateNaissance' => 'date',
            'phoneNumber' => 'required|string',
        ]);

        //create user
        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' =>Hash::make($attrs['password']),
            'dateNaissance' => $attrs['dateNaissance'],
            'phoneNumber' => $attrs['phoneNumber'],
        ]);
        $token = $request->user()->createToken($request->token_name);
        //return user & token in response
        return response([
            'user' => $user,
            'token' => $token->plainTextToken
        ], 200);
    }

    // login user
    public function login(Request $request)
    {
        //validate fields
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // attempt login
        if(!Auth::attempt($attrs))
        {
            return response([
                'message' => 'Informations de connexion non reconnues .'
            ], 403);
        }
        $user=User::where('email',$request->email)->first();
        //return user & token in response
        return response([
            'user' => auth()->user(),
            'token' => $user->createToken($request->token_name)->plainTextToken
        ], 200);
    }

    // logout user
    public function logout()
    {
            Auth::logout();
            return response(['message' => 'Déconnexion réussie !'], 200);
    }

    // get user details
    public function user()
    {
        return response([
            'user' => auth()->user(),
        ], 200);
    }



}
