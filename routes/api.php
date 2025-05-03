<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']);

// El where se utiliza para validar que el id sea un número, lo pongo asi porque si no la ruta /posts/ultimosPosts no va, ya que entra en conflicto con la ruta /posts/{id}

Route::get('/posts/{id}', [PostController::class, 'show'])->where('id', '[0-9]+');

//Poniendo el ? al id, le decimos que es opcional, por lo tanto si no se pasa el id, se ejecuta la funcion ultimosPosts

Route::get('/posts/ultimosPosts/{id?}', [PostController::class, 'ultimosPosts']);

Route::middleware("verificarUsuario")->group(function(){

    Route::post('/posts', [PostController::class, 'store']);

    Route::patch('/posts/{id}', [PostController::class, 'update']);

    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    Route::get('/posts/adopted/{id}', [PostController::class, 'adoptedPosts']);
    
});


