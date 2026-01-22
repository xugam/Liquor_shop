<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSalesRequest extends FormRequest
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

            'items' => 'required|array|min:1',
            'items.*.location_id' => 'required|exists:locations,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:product_units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'nullable|numeric|min:1'

            // Cheque details (conditional)
            // 'cheque.customer_name' => 'required_if:payment_type,cheque',
            // 'cheque.bank_name' => 'required_if:payment_type,cheque',
            // 'cheque.cheque_number' => 'required_if:payment_type,cheque',
            // 'cheque.amount' => 'required_if:payment_type,cheque',
            // 'cheque.cheque_date' => 'required_if:payment_type,cheque|date',
            // 'cheque.cashable_date' => 'required_if:payment_type,cheque|date',
        ];
    }
}
