<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
{
    public function rules()
    {
        $userId = auth()->user()->id;
        return [
            'title' => [
                'required',
                'max:100',
                // 'unique:tasks,title',
                Rule::unique('tasks', 'title')->where('user_id', $userId)->whereNull('task_id')
             ],
            'content' => ['required'],
            'parentTitle' => [
                'sometimes',
                Rule::exists('tasks', 'title')->where('user_id', $userId)
            ],
            'attachment' => ['sometimes', 'image', 'file', 'max:4096', 'mimes:jpg,jpeg,png'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'user_id' => auth()->user()->id,
            'status' => "To-do"
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'success' => false,
            'message' => $validator->messages(),
            'messages' => $validator->messages(),
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 422));
    }

    public function messages()
    {
        return [
            'title.unique' => 'Task title already exists',
        ];

    }
}
