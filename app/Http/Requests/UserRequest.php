<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation(): void
{
    if (is_string($this->roles)) {
        $this->merge([
            'roles' => [$this->roles]
        ]);
    }
}

public function rules(): array
{
    $userId = $this->route('user')?->id;

    return [
        'nombre'   => 'required|string|min:3|max:255',
        'email'    => [
            'required',
            'email',
          Rule::unique('users', 'email')->ignore($userId),
        ],
        'password' => $this->isMethod('POST') ? 'required|string|min:8' : 'nullable|string|min:8',
        'estado'   => 'required|boolean',
        'roles'    => 'sometimes|array',
        'roles.*'  => 'exists:roles,name',
    ];
}

    public function messages(): array
    {
        return [

            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no debe exceder 255 caracteres.',

            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no es válido.',
            'email.unique' => 'El correo ya existe.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',

            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol no existe.',

            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(

            response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422)

        );
    }
}