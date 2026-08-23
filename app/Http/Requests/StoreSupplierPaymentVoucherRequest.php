<?php

namespace App\Http\Requests;

use App\Models\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierPaymentVoucherRequest extends FormRequest
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
            'uid' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'supplier_id' => 'required',
            'credit_account_id' => ['required'],
            'debit_account_id' => ['required'],
            'payee_name' => 'required',
            'narration' => 'nullable',
            'reference_no' => 'nullable',
        ];
    }
    public function prepareForValidation()
    {
        $payableAccountId = ChartOfAccount::query()
            ->where('default_type', 'accounts_payable')
            ->where('status', 'active')
            ->value('id') ?? 22;

        $this->merge([
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'debit_account_id' => $payableAccountId,
        ]);
    }
}
