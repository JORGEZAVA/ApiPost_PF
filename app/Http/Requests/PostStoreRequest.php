<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nameAnimal'=>'required | string | max:255 | unique:posts,nameAnimal',
            'typeAnimal'=>'required | string | in:perro,gato',
            'description'=>'required | string | max:255',
            'image'=>' image | mime:jpeg,png,jpg,gif,svg | max:2048',
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
