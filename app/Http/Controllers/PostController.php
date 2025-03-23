<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class PostController extends Controller
{
    /*
    Habia creado esta fucnione ya que necesitaba obtener la id del usuario de alguna forma pero es que estoy haciendo algo parecido en el middleware y no me a 
    convencido hacer esto, por eso he encontrado otra forma de hacerlo desde el propio middleware y ahi esta la explicacion de como lo he hecho.

    private function obtenerIdUsuario(){

        $token = Request::header("Authorization");
        $response = Http::withToken($token)->get("http://localhost/ApiUsuario_PF/public/api/validarToken");
        $data = $response->json();
        $userId = $data["user"]["id"];

        return $userId;

    }
    */
    public function index()
    {
        $posts = Post::paginate(10);

        if($posts->isEmpty()){
            $data=[
                "mensaje"=>"No se encontraron posts",
                "status"=>404,
            ];
            return response()->json($data,404);
        }

        $data=[
            "mensaje"=>"Listado de posts",
            "posts"=>$posts,
            "status"=>200
        ];

        return response()->json($data,200);
    }

    public function store(PostStoreRequest $request)
    {

        $post = Post::create([
            "nameAnimal"=>$request->nameAnimal,
            "typeAnimal"=>$request->typeAnimal,
            "description"=>$request->description,
            "image"=>$request->image,
            "user_id"=>$request->user_id,  
        ]);

        if(!$post){
            $data=[
                "mensaje"=>"ERROR al crear un post",
                "status"=>500,
            ];

            return response()->json($data,500);
        }

        $data=[
            "mensaje" => "El post se ha creado correctamente",
            "status"=>201,    
        ];

        return response()->json($data,201);
    }

    public function show($identificador)
    {
        $post = Post::find($identificador);

        if(!$post){
            $data=[
                "mensaje"=>"No se encotro ningun post",
                "status"=>404,
            ];

            return response()->json($data,404);
        }

        $data=[
            "mensaje"=>"Post encontrado",
            "post"=>$post,
            "status"=>200,
        ];

        return response()->json($data,200);
    }
    public function update(PostUpdateRequest $request, $identificador)
    {
        $post = Post::find($identificador);

        if(!$post){
            return response()->json([
                "mensaje"=>"No se ha encotrado ningun post para actualizar",
                "status"=>404,
            ],404);
        }

        if($request->has('nameAnimal')){
            $post->nameAnimal=$request->nameAnimal;
        }
        if($request->has('typeAnimal')){
            $post->typeAnimal=$request->typeAnimal;
        }
        if($request->has('description')){
            $post->description=$request->description;
        }
        if($request->has('image')){
            $post->image=$request->image;
        }
        $post->save();

        return response()->json([
            "mensaje"=>"El post ha sido actualizado con exito",
            "status"=>201,
        ],201);
    }

    public function destroy($identificador)
    {
        $post = Post::find($identificador);

        if(!$post){
            $data=[
                "mensaje"=>"No se encontro ningun post",
                "status"=>404,
            ];

            return response()->json($data,404);
        }

        $post->delete();

        return response()->json([
            "mensaje"=> "El post ha sido elimiado con exito",
            "status"=>200,
        ],200);
    }
}
