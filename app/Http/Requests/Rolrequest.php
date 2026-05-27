<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

public function rules(): array
{
    $roleId = $this->route('rol')?->id;

    return [
        'name' => [
            'required',
            'string',
            'min:3',
            'max:20',
            Rule::unique('roles', 'name')->ignore($roleId),
        ],
        'permissions' => 'sometimes|array',
        'permissions.*' => 'exists:permissions,name',
    ];
}
  public function messages(): array
    {
        return [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.string' => 'El nombre del rol debe ser una cadena de texto.',
            'name.min' => 'El nombre del rol debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre del rol no debe exceder 20 caracteres.',
            'name.unique' => 'El nombre del rol ya existe.',
            'permissions.array' => 'Los permisos deben ser un arreglo.',
            'permissions.*.exists' => 'Uno o más permisos no existen.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}