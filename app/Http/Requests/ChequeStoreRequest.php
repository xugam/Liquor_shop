<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChequeStoreRequest extends FormRequest
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

            'customer_name' => 'required|string',
            'bank_name' => 'required|string',
            'cheque_number' => 'required|string',
            'amount' => 'required|numeric',
            'cheque_date' => 'required|date',
            'cashable_date' => 'required|date',
            'reminder_date' => 'required|date',
            'phone_no' => 'required|string|min:10|max:10',
            'status' => 'nullable|in:pending,deposited,cleared,bounced',
        ];
    }
}
