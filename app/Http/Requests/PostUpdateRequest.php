<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class PostUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nameAnimal'=>'string | max:255 | unique:posts,nameAnimal'. $this->route('identificador'),
            'typeAnimal'=>'in:perro,gato',
            'description'=>'string | max:255',
            'image'=>'image | mime:jpeg,png,jpg,gif,svg | max:2048',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors=$validator->errors();

        $response=[
            "mensaje"=>"Error en la validacion para crear un post",
            "errores"=>$errors,
            "status"=>422,
        ];

        throw new ValidationException($validator,$response);
    }
}
