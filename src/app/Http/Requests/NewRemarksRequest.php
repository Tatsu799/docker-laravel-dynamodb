<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewRemarksRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "newRemarks" => "required|array",
            "newRemarks.*.name" => 'required|string|max:255',
            "newRemarks.*.body" => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            "newRemarks.required" => "The remarks field is required.",
            "newRemarks.array" => "The remarks field must be an array.",
            "newRemarks.*.name.required" => "Each remark must have a name.",
            "newRemarks.*.name.string" => "The name must be a string.",
            "newRemarks.*.name.max" => "The name may not be greater than 255 characters.",
            "newRemarks.*.body.string" => "The body must be a string.",
            "newRemarks.*.body.max" => "The body may not be greater than 255 characters.",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
