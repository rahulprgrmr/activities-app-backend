<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Libraries\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticatedTokenController extends Controller
{
    public function store(Request $request)
    {
        $email = trim($request->post('email'));
        $password = trim($request->post('password'));

        if (!$email || !$password)
        {
            return Response::error('Email/Password required');
        }

        $user = User::where('email', $email)->first();

        if (!$user)
        {
            return Response::error('Invalid credentials!');
        }

        if (!Hash::check($password, $user->password)) {
            return Response::error('Invalid credentials!');
        }
        $token = $user->createToken('auth_token');
        $token = $token->plainTextToken;

        return Response::success('Logged In Successfully', [
            'token' => $token
        ]);
    }
}
