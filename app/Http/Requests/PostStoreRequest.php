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
            'nameAnimal'=>'required|string|min:3|max:255|unique:posts,nameAnimal',
            'typeAnimal'=>'required|string|in:perro,gato',
            'description'=>'required|string|min:10|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /* Definimos mensajes personalizados para cada validacion asi cuando se produzca error y
     devuelva el objeto de errors, cada array se vera asi 
     "errors": {
        "email": [
        "El correo electrónico ya ha sido registrado."
        ],
        "password": [
        "La contraseña debe tener al menos 8 caracteres."
        ]
    */
    public function messages(): array
    {
        return [
            'nameAnimal.required' => 'El :attribute es obligatorio.',
            'nameAnimal.string'   => 'El :attribute debe ser una cadena de texto.',
            'nameAnimal.min'      => 'El :attribute debe tener al menos :min caracteres.',
            'nameAnimal.max'      => 'El :attribute no puede tener más de :max caracteres.',
            'nameAnimal.unique'   => 'El :attribute ya existe en la base de datos.',
    
            'typeAnimal.required' => 'El :attribute es obligatorio.',
            'typeAnimal.string'   => 'El :attribute debe ser una cadena de texto.',
            'typeAnimal.in'       => 'El :attribute debe ser uno de los siguientes valores: perro, gato.',
    
            'description.required' => 'La :attribute es obligatoria.',
            'description.string'   => 'La :attribute debe ser una cadena de texto.',
            'description.min'      => 'La :attribute debe tener al menos :min caracteres.',
            'description.max'      => 'La :attribute no puede tener más de :max caracteres.',
    
            'image.required' => 'La :attribute es obligatoria.',
            'image.image'    => 'La :attribute no es válida.',
            'image.mimes'    => 'La :attribute no tiene un formato válido.',
            'image.max'      => 'La :attribute no puede pesar más de 2MB.',
        ];
    }
    
    /* El atribute es para cambiar el nombre de los atributos en los mensajes de error
        por defecto devuelve el nombre del atributo en la base de datos, pero podemos cambiarlo
        por el nombre que queramos, en este caso lo he cambiado por el nombre en español
        para que sea mas entendible para el usuario */
    public function attributes(): array
    {
        return [
            'nameAnimal'  => 'nombre',
            'typeAnimal'  => 'tipo',
            'description' => 'descripción',
            'image'       => 'imagen',
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
