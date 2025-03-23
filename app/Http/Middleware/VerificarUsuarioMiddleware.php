<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class VerificarUsuarioMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");

        if (!$token) {
            return response()->json(["error" => "No autenticado"], 401);
        }

        $response = Http::withToken($token)->get("http://localhost/ApiUsuario_PF/public/api/validarToken");

        if (!$response->successful()) {
            return response()->json(["error" => "Token inválido o usuario no autenticado"], 401);
        }

        //El response lo pasamos a formato json y de ahi obtener la id del usuario
        $data = $response->json();
        $userId = $data['user']['id'];
        /*Con esta funcion del request podemos añadir un nuevo campo a la request que es el user_id
        y le pasamos la id del usuario que hemos obtenido del token. Se pasa como key=valor en forma de array*/
        $request->merge(['user_id' => $userId]);

        return $next($request);
    }
}
