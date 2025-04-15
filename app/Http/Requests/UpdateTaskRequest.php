<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize()
    {
        $task = $this->route('task');
        return auth()->user()->id === $task->user_id;
    }

    public function rules()
    {
        $task = $this->route('task');

        return [
            'title' => ['sometimes', 'max:100', Rule::unique('tasks', 'title')->ignore($task->title, 'title')],
            'content' => ['sometimes'],
            'status' => ['sometimes', 'in:In Progress,Done'],
            'attachment' => ['sometimes', 'image', 'file', 'max:4096', 'mimes:jpg,jpeg,png'],
        ];
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
