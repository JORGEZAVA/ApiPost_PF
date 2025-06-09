<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header("Authorization");

        if (!$token) {
            $data=[
                "error" => "No autenticado",
                "status"=>401,
            ];
            return response()->json($data, 401);
        }

        $response = Http::withToken($token)->get("https://apipost-pf.onrender.com/apivalidarToken");

        if (!$response->successful()) {
            $data=[
                "error" => "Token inválido o usuario no autenticado",
                "status"=>401,
            ];
            return response()->json($data, 401);
        }

        $data = $response->json();
       
        if ($data['usuario']['role'] !== 'admin') {
            
            $data=[
                "error" => "Acceso no autorizado. Se requiere rol de administrador.",
                "status" => 403,
            ];

            return response()->json($data, 403);
        }


        return $next($request);
    }
}
