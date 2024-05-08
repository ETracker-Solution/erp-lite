@extends('layouts.app')
@section('title')
    Sale
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Data Admin Module'=>'',
       'Account Setting'=>'',
       'Chart Of Accounts'=>'',
        ]
    @endphp
    <x-breadcrumb title='Chart Of Accounts' :links="$links"/>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Chart Of Accounts</h3>
                            <div class="card-tools">

                            </div>
                        </div>
                        <div class="card-body" id="inventoryItems">
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Chart of Inventory Create</h4>
                        </div>
                        <div class="card-body">
                            <div class="row callout callout-success">
                                <div class="col-xl-12 col-md-12 col-12">
                                    <x-forms.text label="Account ID" inputName="item_id" placeholder=""
                                                  :isRequired='false' :isReadonly='true' defaultValue=""/>
                                </div>
                                <div class="col-xl-12 col-md-12 col-12">
                                    <x-forms.text label="Account Name" inputName="item_name"
                                                  placeholder="Enter Item Name" :isRequired='false'
                                                  :isReadonly='false' defaultValue=""/>
                                </div>
                                <div class="col-xl-12 col-md-12 col-12">
                                    <x-forms.text label="Account Type" inputName="item_type" placeholder="Enter Name"
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
                            </div>

                            <div class="row  callout callout-secondary" id="addNewForm" hidden>
                                <div class="col-xl-12 col-md-12 col-12">
                                    <x-forms.static-select label="Account Type" inputName="new_item_type"
                                                           placeholder="Select Type" :isRequired='false'
                                                           :isReadonly='false' defaultValue=""
                                                           :options="['group','ledger']"/>
                                </div>
                                <div class="col-xl-12 col-md-12 col-12">
                                    <x-forms.text label="Account Name" inputName="new_item_name"
                                                  placeholder="Enter Account Name" :isRequired='false'
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
            getAccounts()
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
        let itemPriceInput = $("input[name=price]")
        let newItemNameInput = $("input[name=new_item_name]")
        let newItemTypeInput = $("select[name=new_item_type]")

        function makeHidden(element) {
            element.prop('hidden', true)
        }

        function makeVisible(element) {
            element.prop('hidden', false)
        }

        function setValue(element, value) {
            element.val(value)
        }

        function getValue(element) {
            return element.val()
        }

        function changeChart(id) {
            event.preventDefault()
            $.ajax({
                url: "/coa-details/" + id,
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
                    setValue(itemPriceInput, '')
                    makeVisible(updateButton)
                    makeVisible(addButton)
                    makeVisible(deleteButton)
                    if (result.item_type === 'ledger') {
                        makeVisible(additionalInfoDiv)
                        setValue(itemUnitInput, result.unit_id)
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
                        url: "/coa-update/" + getValue(itemIdInput),
                        data: {
                            item_name: getValue(itemNameInput),
                            unit: getValue(itemUnitInput),
                            price: getValue(itemPriceInput),
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (result) {
                            if(result.success){
                                toastr.success(result.message)
                                getAccounts()
                            }else{
                                toastr.error(result.message)
                            }
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
                toastr.error('Please Select Account')
                return
            }
            if (!getValue(newItemNameInput)) {
                toastr.error('Please Enter Account Name')
                return
            }
            if (!getValue(newItemTypeInput)) {
                toastr.error('Please Select Account Type')
                return
            }
            $.ajax({
                method: 'POST',
                url: "/coa-store/" + getValue(itemIdInput),
                data: {
                    item_name: getValue(newItemNameInput),
                    item_type: getValue(newItemTypeInput),
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (result) {
                    if(result.success){
                        toastr.success(result.message)
                        getAccounts()
                    }else{
                        toastr.error(result.message)
                    }

                }
            });
        }

        function deleteInvItem() {
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Account')
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
                        url: "/coa-delete/" + getValue(itemIdInput),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (result) {
                            toastr.success(result.message)
                            getAccounts()
                        }
                    });
                }
            });
            ;
        }

        function getAccounts() {
            $.ajax({
                url: "/coa-items/",
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
                    setValue(itemPriceInput, 0)

                    makeHidden(addNewDiv)
                    makeHidden(additionalInfoDiv)

                    makeHidden(addButton)
                    makeHidden(updateButton)
                    makeHidden(saveButton)
                    makeHidden(deleteButton)
                }
            });
        }
    </script>
@endpush
