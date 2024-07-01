<?php

namespace App\Http\Requests;

use App\Classes\RequisitionNumber;
use App\Models\Outlet;
use App\Models\Requisition;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreRMRequisitionRequest extends FormRequest
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
            'from_store_id' => 'required',
            'to_store_id' => 'required',
            'from_factory_id' => 'required',
            'products' => 'array',
            'date' => 'required',
            'uid' => 'required',
            'type' => 'required',
            'reference_no' => 'nullable',
            'total_item' => 'required',
            'total_quantity' => 'required',
            'remark' => 'nullable',
            'created_by' => 'required',
        ];
    }

    public function prepareForValidation()
    {
        Outlet::find(Store::find($this->from_store_id)->doc_id);
        $this->merge([
            'from_factory_id'=>getStoreDocId($this->from_store_id),
//            'uid' => RequisitionNumber::serial_number(),
            'uid' => generateUniqueUUID(Outlet::find(Store::find($this->from_store_id)->doc_id)->id, Requisition::class,'uid'),
            'type' => 'RM',
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
