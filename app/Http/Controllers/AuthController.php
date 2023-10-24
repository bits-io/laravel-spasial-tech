<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:dns|max:200',
            'password' => 'required|max:200',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error(
                'Validation error',
                400,
                $validator->errors()
            );
        }

        try {
            // VARIABLE EMAIL AND PASSWORD
            $email = $request->email;
            $password = $request->password;

            // CHECK EMAIL AND PASSWORD
            $user = User::where('email', $email)->first();
            if (!$user || !Hash::check($password, $user->password)) {
                return ResponseHelper::error('Email or password wrong', 401);
            }

            // CREATE TOKEN
            // $token = $this->createNewToken(auth()->attempt($user));
            $token = JWTAuth::fromUser($user);

            $user->remember_token = $token;
            $user->save();

            // RESPONSE
            return ResponseHelper::success(
                [
                    'user' => $user,
                    'token' => $token
                ],
                'Login successfully',
                200
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    public function register()
    {

    }
}
