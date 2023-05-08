<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseTrait;
    public function register(Request $request)
    {


        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:8'
        ]);
        
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('api_token-' . $request->userAgent() . $user->id)->plainTextToken;

            return $this->successResponse(['token' => $token]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

}