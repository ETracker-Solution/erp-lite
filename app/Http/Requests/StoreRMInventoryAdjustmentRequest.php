<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreRMInventoryAdjustmentRequest extends FormRequest
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
            'store_id' => 'required',
            'products' => 'required|array|min:1',
            'products.*.coi_id' => 'required|integer',
            'products.*.quantity' => 'required|numeric|gt:0',
            'products.*.rate' => 'required|numeric|gte:0',
            'date' => 'required',
            'reference_no' => 'nullable',
            'remark' => 'nullable',
            'status' => 'nullable',
            'created_by' => 'required',
            'transaction_type' => 'required|in:increase,decrease',
            'subtotal' => 'required',
            'type' => 'required|in:RM',
        ];
    }

    public function prepareForValidation()
    {
        $products = $this->input('products');

        if ($this->filled('products_payload')) {
            $decoded = json_decode($this->input('products_payload'), true);
            if (is_array($decoded) && $decoded !== []) {
                $products = $decoded;
            }
        }

        if (is_array($products)) {
            $products = array_values(array_map(static function ($product) {
                return [
                    'coi_id' => (int) ($product['coi_id'] ?? 0),
                    'quantity' => (float) ($product['quantity'] ?? 0),
                    'rate' => (float) ($product['rate'] ?? 0),
                ];
            }, $products));
        }

        $this->merge([
            'products' => $products,
            'type' => 'RM',
            'status' => 'adjusted',
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);
    }
}
