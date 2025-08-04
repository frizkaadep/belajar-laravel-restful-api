<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "street" => ['nullable', 'string', 'max:255'],
            "city" => ['nullable', 'string', 'max:100'],
            "province" => ['nullable', 'string', 'max:100'],
            "country" => ['required', 'string', 'max:100'],
            "postal_code" => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => [
                'message' => $validator->getMessageBag(),
            ]
        ], 400));
    }
}
