@extends('layouts.app')
@section('title')
    Chart Of Inventory
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Master Data'=>'',
       'Inventory Setting'=>'',
       'Inventory Item List'=>'',
        ]
    @endphp
    <x-breadcrumb title='Inventory Item List' :links="$links"/>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Chart Of Inventory</h3>
                            <div class="card-tools">

                            </div>
                        </div>
                        <div class="card-body" id="inventoryItems">
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <form action="{{ route('chart-of-inventories.store') }}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <div class="card card-info">
                            <div class="card-header">
                                <h4 class="card-title">Chart of Inventory Entry</h4>
                            </div>
                            <div class="card-body">
                                <div class="row callout callout-success">
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.text label="Item ID" inputName="item_id" placeholder=""
                                                      :isRequired='false' :isReadonly='true' defaultValue=""/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.text label="Item Name" inputName="item_name"
                                                      placeholder="Enter Item Name" :isRequired='false'
                                                      :isReadonly='false' defaultValue=""/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.text label="Item Type" inputName="item_type" placeholder="Enter Name"
                                                      :isRequired='false' :isReadonly='true' defaultValue=""/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.text label="Group Name" inputName="group_name" placeholder="Enter Name"
                                                      :isRequired='false' :isReadonly='true' defaultValue=""/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.text label="Account Type" inputName="account_type" placeholder=""
                                                      :isRequired='false' :isReadonly='true' defaultValue=""/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <label for="">Non Discountable</label>
                                        <select name="non_discountable" id="" class="form-control">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row  callout callout-secondary" id="addNewForm" hidden>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.static-select label="Item Type" inputName="new_item_type"
                                                               placeholder="Select Type" :isRequired='false'
                                                               :isReadonly='false' defaultValue=""
                                                               :options="['group','item']"/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.text label="Item Name" inputName="new_item_name"
                                                      placeholder="Enter Item Name" :isRequired='false'
                                                      :isReadonly='false' defaultValue=""/>
                                    </div>
                                </div>

                                <div class="row callout callout-info" id="additionalInfo" hidden>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.select label="Unit of Measurement" inputName="unit"
                                                        placeholder="Select Unit" :isRequired='true'
                                                        :isReadonly='false' defaultValue=""
                                                        :options="$units" optionId="id" optionValue="name"/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.text label="Selling Price" inputName="price"
                                                      placeholder="Enter Selling Price" :isRequired='false'
                                                      :isReadonly='false' defaultValue=""/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.static-select label="Status" inputName="status" placeholder="Select One"
                                            :isRequired='true' :isReadonly='false' defaultValue="active"
                                            :options="['active','inactive']" />
                                    </div>
                                </div>

                                <div class="row callout callout-info" id="vatlInfo">
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.static-select
                                            label="VAT + SD Type"
                                            inputName="vat_type"
                                            placeholder="Select VAT + SD Type"
                                            :isRequired="true"
                                            :isReadonly="false"
                                            :options="['including', 'excluding', 'zero']"
                                            defaultValue=""
                                        />
                                    </div>

                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.text label="Vat Amount(%)" inputName="vat_amount"
                                                      placeholder="Enter Vat Amount" :isRequired='true'
                                                      :isReadonly='false' defaultValue=""/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <x-forms.text label="SD Amount(%)" inputName="sd_amount"
                                                      placeholder="Enter SD Amount" :isRequired='true'
                                                      :isReadonly='false' defaultValue=""/>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer ">
                                <div class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary" hidden id="addButton"
                                            onclick="addNewItem()">Add
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info" hidden id="saveButton"
                                            onclick="submitItem()">Save
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" hidden id="updateButton"
                                            onclick="updateItem()">
                                        Update
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" hidden id="deleteButton"
                                            onclick="deleteInvItem()">Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div> <!-- end col -->
        </div>
        <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@push('script')
    <script>

        $(document).ready(function () {
            getInventoryItems()
        })

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        let addButton = $('#addButton')
        let saveButton = $('#saveButton')
        let updateButton = $('#updateButton')
        let deleteButton = $('#deleteButton')

        let additionalInfoDiv = $('#additionalInfo')
        let addNewDiv = $('#addNewForm')

        let itemIdInput = $("input[name=item_id]")
        let itemNameInput = $("input[name=item_name]")
        let itemTypeInput = $("input[name=item_type]")
        let groupNameInput = $("input[name=group_name]")
        let accountTypeInput = $("input[name=account_type]")
        let itemUnitInput = $("select[name=unit]")
        let itemStatusInput = $("select[name=status]")
        let itemPriceInput = $("input[name=price]")
        let newItemNameInput = $("input[name=new_item_name]")
        let newItemTypeInput = $("select[name=new_item_type]")
        let nonDiscountableCheckbox = $("select[name=non_discountable]")
        let vatTypeInput = $("select[name=vat_type]");
        let vatAmountInput = $("input[name=vat_amount]");
        let sdAmountInput = $("input[name=sd_amount]");

        function makeHidden(element) {
            element.prop('hidden', true)
        }

        function makeVisible(element) {
            element.prop('hidden', false)
        }

        function setValue(element, value) {
            if (element == itemUnitInput || element == itemStatusInput || element == nonDiscountableCheckbox || element == vatTypeInput) {
                element.val(value).trigger('change')
            } else {
                element.val(value)
            }
        }

        function getValue(element) {
            return element.val()
        }

        function changeChart(id) {
            event.preventDefault()
            $.ajax({
                url: "/inventory-details/" + id,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (result) {
                    setValue(itemIdInput, result.item_id)
                    setValue(itemNameInput, result.item_name)
                    setValue(itemTypeInput, result.item_type)
                    setValue(groupNameInput, result.group_name)
                    setValue(accountTypeInput, result.account_type)
                    setValue(itemUnitInput, '')
                    setValue(itemStatusInput, '')
                    setValue(itemPriceInput, '')
                    setValue(nonDiscountableCheckbox, result.non_discountable)

                    setValue(vatTypeInput, result.vat_type);
                    setValue(vatAmountInput, result.vat_amount);
                    setValue(sdAmountInput, result.sd_amount);

                    makeVisible(updateButton)
                    makeVisible(addButton)
                    makeVisible(deleteButton)
                    if (result.item_type === 'item') {
                        makeVisible(additionalInfoDiv)
                        setValue(itemUnitInput, result.unit_id)
                        setValue(itemStatusInput, result.status)
                        setValue(itemPriceInput, result.price)
                        makeHidden(addButton)
                    } else {
                        makeHidden(additionalInfoDiv)
                    }


                }
            });
        }

        function updateItem() {
            event.preventDefault()
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Item')
                return
            }
            if (!getValue(itemNameInput)) {
                toastr.error('Please Enter Item Name')
                return
            }

            Swal.fire({
                title: "Are You Sure!",
                text: "Update this Item!",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: 'POST',
                        url: "/inventory-update/" + getValue(itemIdInput),
                        data: {
                            item_name: getValue(itemNameInput),
                            unit: getValue(itemUnitInput),
                            price: getValue(itemPriceInput),
                            status: getValue(itemStatusInput),
                            non_discountable: getValue(nonDiscountableCheckbox),
                            vat_type: getValue(vatTypeInput),
                            vat_amount: getValue(vatAmountInput),
                            sd_amount: getValue(sdAmountInput),
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (result) {
                            toastr.success(result.message)
                            getInventoryItems()
                        }
                    });
                }
            });
        }

        function addNewItem() {
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Item')
                return
            }

            itemNameInput.prop('disabled', true)

            makeVisible(addNewDiv)
            makeVisible(saveButton)
            makeHidden(addButton)
            makeHidden(updateButton)
            makeHidden(deleteButton)

        }

        function submitItem() {
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Item')
                return
            }
            if (!getValue(newItemNameInput)) {
                toastr.error('Please Enter Item Name')
                return
            }
            if (!getValue(newItemTypeInput)) {
                toastr.error('Please Select Item Type')
                return
            }
            $.ajax({
                method: 'POST',
                url: "/inventory-store/" + getValue(itemIdInput),
                data: {
                    item_name: getValue(newItemNameInput),
                    item_type: getValue(newItemTypeInput),
                    unit: getValue(itemUnitInput),
                    status: getValue(itemStatusInput),
                    price: getValue(itemPriceInput),
                    non_discountable: getValue(nonDiscountableCheckbox),
                    vat_type: getValue(vatTypeInput),
                    vat_amount: getValue(vatAmountInput),
                    sd_amount: getValue(sdAmountInput),
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (result) {
                    toastr.success(result.message)
                    getInventoryItems()
                }
            });
        }

        function deleteInvItem() {
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Item')
                return
            }

            Swal.fire({
                title: "Are You Sure!",
                text: "Delete this Item!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: 'DELETE',
                        url: "/inventory-delete/" + getValue(itemIdInput),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (result) {
                            toastr.success(result.message)
                            getInventoryItems()
                        }
                    });
                }
            });
            ;
        }

        function getInventoryItems() {
            $.ajax({
                url: "/inventory-items/",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (result) {
                    $('#inventoryItems').html(result)
                    setValue(newItemNameInput, '')
                    setValue(newItemTypeInput, '')
                    setValue(itemIdInput, '')
                    setValue(itemNameInput, '')
                    setValue(itemTypeInput, '')
                    setValue(groupNameInput, '')
                    setValue(accountTypeInput, '')
                    setValue(itemUnitInput, '')
                    setValue(itemStatusInput, '')
                    setValue(itemPriceInput, 0)

                    makeHidden(addNewDiv)
                    makeHidden(additionalInfoDiv)
                }
            });
        }

        newItemTypeInput.on('change', function () {
            if ($(this).val() === 'item') {
                makeVisible(additionalInfoDiv)
            } else {
                makeHidden(additionalInfoDiv)
            }
        })
    </script>
@endpush
