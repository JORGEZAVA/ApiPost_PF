<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate("cascade");
            $table->string('nameAnimal')->unique();
            $table->string('typeAnimal');
            $table->string('description');

            $table->boolean('adopted')->default(false);
            $table->timestamps();
        });
        // Luego, modifica la tabla con una sentencia raw para cambiar la columna a MEDIUMBLOB
        DB::statement('ALTER TABLE posts ADD COLUMN image MEDIUMBLOB AFTER description');
    }


    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
