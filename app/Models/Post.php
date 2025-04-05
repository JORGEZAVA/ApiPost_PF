<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Post extends Model
{
    use HasFactory, Notifiable, HasApiTokens ;

    protected $table='posts';

    protected $fillable = [
        'nameAnimal',
        'typeAnimal',
        'description',
        "image",
        "user_id",
    ];


    protected function casts(): array
    {
        return [
            "adopted"=>  "boolean",
        ];
    }

    public function getImageAttribute($value)
    {
        return base64_encode($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
