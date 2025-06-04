<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']);

Route::get('/posts/verifiedUser/{id}', [PostController::class, 'showPostsVerifiedUser']);

//Poniendo el ? al id, le decimos que es opcional, por lo tanto si no se pasa el id, se ejecuta la funcion ultimosPosts

Route::get('/posts/ultimosPosts/{idUsuario?}', [PostController::class, 'ultimosPosts']);

Route::get('/posts/adopted/{idUsuario}', [PostController::class, 'adoptedPosts']);

// El where se utiliza para validar que el id sea un número, lo pongo asi porque si no la ruta /posts/ultimosPosts no va, ya que entra en conflicto con la ruta /posts/{id}

Route::get('/posts/{id}', [PostController::class, 'show'])->where('id', '[0-9]+');

Route::middleware("verificarUsuario")->group(function(){

    Route::post('/posts', [PostController::class, 'store']);

    Route::patch('/posts/{id}', [PostController::class, 'update']);

    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    Route::middleware("verificarAdmin")->group(function(){

        Route::get('/posts/noVerificados', [PostController::class, 'noVerificados']);

        Route::patch('/posts/{id}/validarPost', [PostController::class, 'verificarPost']);

    });
  
});


