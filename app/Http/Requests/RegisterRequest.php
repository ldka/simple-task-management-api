<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:190'],
            'last_name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'unique:users,email', ' max:255'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ]
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'password_confirmation' => $this->passwordConfirmation,
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'success' => false,
            'message' => $validator->messages(),
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 422));
    }
}
