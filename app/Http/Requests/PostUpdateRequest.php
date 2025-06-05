<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Models\Post;

class PostUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nameAnimal'=>'string|min:3|max:255|unique:posts,nameAnimal,'.$this->route('id'),
            'typeAnimal'=>'in:perro,gato',
            'description'=>'string|min:10|max:255',
            'image'=>'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            "race" => "string|min:3",
            'vaccines'     => ['sometimes', 'array'],
            'vaccines.*'   => ['string', Rule::in(array_keys(Post::$VACCINES))],
        ];
    }

    public function messages(): array
    {
        return [
            
            'nameAnimal.string'   => 'El :attribute debe ser una cadena de texto.',
            'nameAnimal.min'      => 'El :attribute debe tener al menos :min caracteres.',
            'nameAnimal.max'      => 'El :attribute no puede tener más de :max caracteres.',
            'nameAnimal.unique'   => 'El :attribute ya existe en la base de datos.',
    
            'typeAnimal.string'   => 'El :attribute debe ser una cadena de texto.',
            'typeAnimal.in'       => 'El :attribute debe ser uno de los siguientes valores: perro, gato.',
    
            'description.string'   => 'La :attribute debe ser una cadena de texto.',
            'description.min'      => 'La :attribute debe tener al menos :min caracteres.',
            'description.max'      => 'La :attribute no puede tener más de :max caracteres.',
    
            'image.image'    => 'La :attribute no es válida.',
            'image.mimes'    => 'La :attribute no tiene un formato válido.',
            'image.max'      => 'La :attribute no puede pesar más de 2MB.',

            'vaccines.array'        => 'Las :attribute deben enviarse como un arreglo.',
            'vaccines.*.string'     => 'Cada vacuna debe ser una cadena de texto.',
            'vaccines.*.in'         => 'Vacuna inválida. Los valores permitidos son: ' . implode(', ', array_keys(Post::$VACCINES)) . '.',
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
            'vaccines'    => 'vacunas',
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
