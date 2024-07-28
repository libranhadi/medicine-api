<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMedicineOutgoingRequest extends FormRequest
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
            'id_medicine' => 'required',
            'unit_id' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'quantity.min' => 'The quantity must be at least :min.',
            'unit_id.required' => 'Please, select unit',
            'unit_id.numeric' => 'Invalid field unit',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $message = $validator->errors()->first() ?: "Validation Error";
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => $message,
            'errors' => $validator->errors(),
        ], 422));
    }
}
