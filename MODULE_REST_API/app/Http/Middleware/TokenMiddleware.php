<?php

namespace App\Http\Middleware;

use App\Models\SocModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $TokenValidator = Validator::make($request->all(), [
            "token" => "required"
        ]);

        if($TokenValidator->fails()) {
            return response()->json([
                "message" => "Unauthorized user"
            ], 401);
        }
        
        if(!SocModel::where("login_tokens", $request->token)->first()) {
            return response()->json([
                "message" => "Unauthorized user"
            ], 401);
        }
        
        return $next($request);
    }
}
