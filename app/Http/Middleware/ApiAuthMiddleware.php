<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $token = $request->header('Authorization');
        $authenticate = true;

        if (!$token) {
            $authenticate = false;
        } else {
            // Decode the JWT token to extract the payload
            $token = str_replace('Bearer ', '', $token); // Remove 'Bearer ' prefix

            // $tokenData = JWTAuth::decode($token
            $tokenParts = explode(".", $token);
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtHeader = json_decode($tokenHeader);
            $jwtPayload = json_decode($tokenPayload);

            // return response()->json($roles[0]);

            $user = User::where('id', $jwtPayload->sub)
                ->where('remember_token', $token)->first();
            if (!$user) {
                $authenticate = false;
            } else {
                Auth::login($user);
            }
        }

        if ($authenticate) {
            return $next($request);
        } else {
            return ResponseHelper::error('Unauthorized', 401);
        }
    }
}
