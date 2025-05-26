<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class PostController extends Controller
{
    // Obtener todos los posts si estan verificados
    public function index($identificadorUsuario = null)
    {
        if ($identificadorUsuario) {
            $posts = Post::query()->where("user_id", $identificadorUsuario)->where("verificado",true)->paginate(10);
        } else {
            $posts = Post::query()->where("verificado",true)->paginate(10);
        }

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

    // Crear post
    public function store(PostStoreRequest $request)
    {
    
        $usuario = User::find($request->user_id);

        if ($usuario->is_banned) {
            return response()->json([
                'mensaje' => 'No tienes permitido publicar posts porque estás baneado.',
                'status' => 403
            ], 403);
        }


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Leer el contenido del archivo
            $imageData = file_get_contents($file->getRealPath());
            // Opcional: Si prefieres almacenarlo en base64, descomenta la siguiente línea:
            // $imageData = base64_encode($imageData);
        } else {
            $imageData = null;
        }

        $post = Post::create([
            "nameAnimal"=>$request->input('nameAnimal'),
            "typeAnimal"=>$request->input('typeAnimal'),
            "description"=>$request->input('description'),
            "image"=>$imageData,
            "user_id"=>$request->input("user_id"),  // Aqui utilizamos el user_id que hemos obtenido del middleware
        ]);

        if(!$post){
            $data=[
                "mensaje"=>"ERROR al crear un post, intenta de nuevo",
                "status"=>400,
            ];

            return response()->json($data,500);
        }

        $data=[
            "mensaje" => "El post se ha creado correctamente",
            "status"=>201,    
        ];

        return response()->json($data,201);
    }

    // Mostrar un post si esta verificado
    public function show($identificador)
    {
        $post = Post::query()->where("id",$identificador)->first();

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

    // Actualizar un post
    public function update(PostUpdateRequest $request, $identificador)
    {
        $post = Post::find($identificador);

        if(!$post){
            $data=[
                "mensaje"=>"No se ha encotrado ningun post para actualizar",
                "status"=>404,
            ];
            return response()->json($data,404);
        }

        if($request->has('nameAnimal')){
            $post->nameAnimal=$request->input('nameAnimal');
        }
        if($request->has('typeAnimal')){
            $post->typeAnimal=$request->input('typeAnimal');
        }
        if($request->has('description')){
            $post->description=$request->input('description');
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Leer el contenido del archivo
            $imageData = file_get_contents($file->getRealPath());
            // Opcional: Si prefieres almacenarlo en base64, descomenta la siguiente línea:
            // $imageData = base64_encode($imageData);
        } else {
            $imageData = null;
        }
        if($imageData!=null){
            $post->image=$imageData;
        }
        
        if($request->has('adopted')){
            $post->adopted=$request->input('adopted');
        }
        if($request->has('userAdopted_id')){
            $post->userAdopted_id=$request->input('userAdopted_id');
        }
        
        $post->save();

        $data=[
            "mensaje"=>"El post ha sido actualizado con exito",
            "status"=>201,
        ];

        return response()->json($data,201);
    }

    // Eliminar un post
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

    // Obtener los ultimos 6 posts 
    public function ultimosPosts($identificador = null){
        if ($identificador) {
            $posts = Post::query()->where("user_id", $identificador)->where("verificado",true)
                ->orderBy("created_at", "desc")
                ->take(6)
                ->get();
        } else {
            $posts = Post::query()->orderBy("created_at", "desc")->where("verificado",true)
                ->take(6)
                ->get();
        }
        
        if($posts->isEmpty()){
            $data=[
                "mensaje"=>"No se encontraron posts",
                "status"=>404,
            ];
            return response()->json($data,404);
        }
        $data=[
            "mensaje"=>"Ultimos posts",
            "posts"=>$posts,
            "status"=>200
        ];
        return response()->json($data,200);
    }

    // Obtener los ultimos posts adoptados
    public function adoptedPosts($identificador){

        $posts = Post::query()->where("userAdopted_id", $identificador)->where("verificado",true)
            ->orderBy("created_at", "desc")
            ->get();

        if($posts->isEmpty()){
            $data=[
                "mensaje"=>"No se han encontrado posts adoptados",
                "status"=>404,
            ];
            return response()->json($data,404);
        };

        $data=[
            "mensaje"=>"Ultimos posts adoptados",
            "posts"=>$posts,
            "status"=>200
        ];

        return response()->json($data,200);
    }

    public function verificarPost($identificador){
        $post = Post::where("id",$identificador)->first();

        if(!$post){
            $data=[
                "mensaje"=>"No se encotro ningun post",
                "status"=>404,
            ];
            return response()->json($data,404);
        }

        $post->verificado=true;
        $post->save();

        $data=[
            "mensaje"=> "Post verificado correctamente",
            "status"=>201,
        ];

        return response()->json($data,201);
    }
    
    public function noVerificados(){
        $posts = Post::where("verificado",false)->paginate(10);

        if($posts->count() === 0){
            $data=[
                "mensaje"=>"No se encontraron posts",
                "status"=>404,
            ];
            return response()->json($data,404);
        }

        $data=[
            "mensaje"=>"Listado de posts no verificados",
            "posts"=>$posts,
            "status"=>200
        ];

        return response()->json($data,200);
    }
    
}
