@extends('layouts.app')
@section('title')
    Chart Of Inventory
@endsection
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Master Data' => '',
            'Inventory Setting' => '',
            'Inventory Item List' => '',
        ];
    @endphp
    <x-breadcrumb title='Inventory Item List' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row erp-chart">
                <div class="col-lg-4 col-md-5 mb-3">
                    <div class="card card-info erp-chart__panel h-100">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Inventory tree</h3>
                            <div class="erp-chart__meta mt-1">Select a group or item to edit</div>
                        </div>
                        <div class="card-body pt-3">
                            <div class="erp-chart__search mb-3">
                                <input type="search" id="coiTreeSearch" class="form-control"
                                       placeholder="Search inventory…" autocomplete="off">
                            </div>
                            <div id="inventoryItems" class="erp-chart__tree-wrap">
                                <div class="erp-chart__loading">
                                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                                    <span>Loading inventory…</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7 mb-3">
                    <div class="card card-info erp-chart__panel h-100">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Inventory details</h4>
                            <div class="erp-chart__meta mt-1">Update items, or add a group/item under a parent</div>
                        </div>
                        <div class="card-body">
                            <div class="row erp-chart__fields">
                                <div class="col-md-6 col-12">
                                    <x-forms.text label="Item ID" inputName="item_id" placeholder=""
                                                  :isRequired='false' :isReadonly='true' defaultValue=""/>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.text label="Item Type" inputName="item_type" placeholder=""
                                                  :isRequired='false' :isReadonly='true' defaultValue=""/>
                                </div>
                                <div class="col-12">
                                    <x-forms.text label="Item Name" inputName="item_name"
                                                  placeholder="Enter Item Name" :isRequired='false'
                                                  :isReadonly='false' defaultValue=""/>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.text label="Parent group" inputName="group_name" placeholder=""
                                                  :isRequired='false' :isReadonly='true' defaultValue=""/>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.text label="Account Type" inputName="account_type" placeholder=""
                                                  :isRequired='false' :isReadonly='true' defaultValue=""/>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label for="non_discountable">Non Discountable</label>
                                    <select name="non_discountable" id="non_discountable" class="form-control">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row erp-chart__fields erp-chart__fields--add mt-2" id="addNewForm" hidden>
                                <div class="col-12">
                                    <div class="erp-chart__section-label">Add under selected group</div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.static-select label="Item Type" inputName="new_item_type"
                                                           placeholder="Select Type" :isRequired='false'
                                                           :isReadonly='false' defaultValue=""
                                                           :options="['group','item']"/>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.text label="Item Name" inputName="new_item_name"
                                                  placeholder="Enter Item Name" :isRequired='false'
                                                  :isReadonly='false' defaultValue=""/>
                                </div>
                            </div>

                            <div class="row erp-chart__fields erp-chart__fields--extra mt-2" id="additionalInfo" hidden>
                                <div class="col-12">
                                    <div class="erp-chart__section-label">Item pricing &amp; unit</div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.select label="Unit of Measurement" inputName="unit"
                                                    placeholder="Select Unit" :isRequired='true'
                                                    :isReadonly='false' defaultValue=""
                                                    :options="$units" optionId="id" optionValue="name"/>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.text label="Selling Price" inputName="price"
                                                  placeholder="Enter Selling Price" :isRequired='false'
                                                  :isReadonly='false' defaultValue=""/>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.static-select label="Status" inputName="status" placeholder="Select One"
                                                           :isRequired='true' :isReadonly='false' defaultValue="active"
                                                           :options="['active','inactive']"/>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="erp-chart__actions">
                                <button type="button" class="btn btn-sm btn-primary" hidden id="addButton"
                                        onclick="addNewItem()">Add child
                                </button>
                                <button type="button" class="btn btn-sm btn-info" hidden id="saveButton"
                                        onclick="submitItem()">Save
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" hidden id="updateButton"
                                        onclick="updateItem()">Update
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" hidden id="deleteButton"
                                        onclick="deleteInvItem()">Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .erp-chart__meta {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.82rem;
            font-weight: 500;
        }

        .erp-chart__search .form-control {
            border-radius: 10px;
            border-color: var(--erp-line, #e6e0d8);
        }

        .erp-chart__tree-wrap {
            max-height: min(70vh, 640px);
            overflow: auto;
            padding-right: 4px;
        }

        .erp-chart__loading {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--erp-muted, #6b625b);
            padding: 24px 8px;
            font-size: 0.9rem;
        }

        .erp-chart__fields .form-group,
        .erp-chart__fields .mb-3 {
            margin-bottom: 0.85rem;
        }

        .erp-chart__fields--add,
        .erp-chart__fields--extra {
            background: var(--erp-accent-soft, #e7f2ec);
            border: 1px solid var(--erp-line, #e6e0d8);
            border-radius: 12px;
            padding: 12px 8px 4px;
            margin-left: 0;
            margin-right: 0;
        }

        .erp-chart__fields--extra {
            background: #f3f7f9;
        }

        .erp-chart__section-label {
            font-weight: 700;
            color: var(--erp-accent-deep, #245540);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .erp-chart__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .erp-chart__actions .btn {
            min-width: 96px;
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function () {
            getInventoryItems();

            $('#coiTreeSearch').on('input', function () {
                filterChartTree($(this).val());
            });
        });

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        let addButton = $('#addButton');
        let saveButton = $('#saveButton');
        let updateButton = $('#updateButton');
        let deleteButton = $('#deleteButton');

        let additionalInfoDiv = $('#additionalInfo');
        let addNewDiv = $('#addNewForm');

        let itemIdInput = $("input[name=item_id]");
        let itemNameInput = $("input[name=item_name]");
        let itemTypeInput = $("input[name=item_type]");
        let groupNameInput = $("input[name=group_name]");
        let accountTypeInput = $("input[name=account_type]");
        let itemUnitInput = $("select[name=unit]");
        let itemStatusInput = $("select[name=status]");
        let itemPriceInput = $("input[name=price]");
        let newItemNameInput = $("input[name=new_item_name]");
        let newItemTypeInput = $("select[name=new_item_type]");
        let nonDiscountableCheckbox = $("select[name=non_discountable]");

        function makeHidden(element) {
            element.prop('hidden', true);
        }

        function makeVisible(element) {
            element.prop('hidden', false);
        }

        function setValue(element, value) {
            if (element.is(itemUnitInput) || element.is(itemStatusInput) || element.is(nonDiscountableCheckbox)) {
                element.val(value).trigger('change');
            } else {
                element.val(value);
            }
        }

        function getValue(element) {
            return element.val();
        }

        function filterChartTree(query) {
            const q = (query || '').toString().trim().toLowerCase();
            const $nodes = $('#inventoryItems .erp-tree-node');

            if (!q) {
                $nodes.show();
                $('#inventoryItems .erp-tree').each(function () {
                    const $ul = $(this);
                    if ($ul.closest('li').length) {
                        $ul.hide();
                    }
                });
                return;
            }

            $nodes.each(function () {
                const $li = $(this);
                const name = ($li.data('name') || '').toString().toLowerCase();
                const match = name.indexOf(q) !== -1;
                const childMatch = $li.find('.erp-tree-node').filter(function () {
                    return (($(this).data('name') || '').toString().toLowerCase().indexOf(q) !== -1);
                }).length > 0;

                if (match || childMatch) {
                    $li.show();
                    if (childMatch) {
                        $li.children('.erp-tree').show();
                    }
                } else {
                    $li.hide();
                }
            });
        }

        function changeChart(id) {
            event.preventDefault();
            $('#inventoryItems .erp-tree-label').removeClass('is-selected');
            $('#inventoryItems .erp-tree-label[data-id="' + id + '"]').addClass('is-selected');

            $.ajax({
                url: "/inventory-details/" + id,
                headers: {'X-CSRF-TOKEN': csrfToken},
                success: function (result) {
                    setValue(itemIdInput, result.item_id);
                    setValue(itemNameInput, result.item_name);
                    setValue(itemTypeInput, result.item_type);
                    setValue(groupNameInput, result.group_name);
                    setValue(accountTypeInput, result.account_type);
                    setValue(itemUnitInput, '');
                    setValue(itemStatusInput, '');
                    setValue(itemPriceInput, '');
                    setValue(nonDiscountableCheckbox, result.non_discountable);
                    itemNameInput.prop('disabled', false);
                    makeVisible(updateButton);
                    makeVisible(addButton);
                    makeVisible(deleteButton);
                    makeHidden(addNewDiv);
                    makeHidden(saveButton);
                    if (result.item_type === 'item') {
                        makeVisible(additionalInfoDiv);
                        setValue(itemUnitInput, result.unit_id);
                        setValue(itemStatusInput, result.status);
                        setValue(itemPriceInput, result.price);
                        makeHidden(addButton);
                    } else {
                        makeHidden(additionalInfoDiv);
                    }
                }
            });
        }

        function updateItem() {
            event.preventDefault();
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Item');
                return;
            }
            if (!getValue(itemNameInput)) {
                toastr.error('Please Enter Item Name');
                return;
            }

            Swal.fire({
                title: "Are You Sure!",
                text: "Update this Item!",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#2f6b4f",
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
                            non_discountable: getValue(nonDiscountableCheckbox)
                        },
                        headers: {'X-CSRF-TOKEN': csrfToken},
                        success: function (result) {
                            if (result.success === false) {
                                toastr.error(result.message);
                                return;
                            }
                            toastr.success(result.message);
                            getInventoryItems();
                        }
                    });
                }
            });
        }

        function addNewItem() {
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Item');
                return;
            }

            itemNameInput.prop('disabled', true);
            makeVisible(addNewDiv);
            makeVisible(saveButton);
            makeHidden(addButton);
            makeHidden(updateButton);
            makeHidden(deleteButton);
        }

        function submitItem() {
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Item');
                return;
            }
            if (!getValue(newItemNameInput)) {
                toastr.error('Please Enter Item Name');
                return;
            }
            if (!getValue(newItemTypeInput)) {
                toastr.error('Please Select Item Type');
                return;
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
                    non_discountable: getValue(nonDiscountableCheckbox)
                },
                headers: {'X-CSRF-TOKEN': csrfToken},
                success: function (result) {
                    if (result.success === false) {
                        toastr.error(result.message);
                        return;
                    }
                    toastr.success(result.message);
                    getInventoryItems();
                }
            });
        }

        function deleteInvItem() {
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Item');
                return;
            }

            Swal.fire({
                title: "Are You Sure!",
                text: "Delete this Item!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#2f6b4f",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: 'DELETE',
                        url: "/inventory-delete/" + getValue(itemIdInput),
                        headers: {'X-CSRF-TOKEN': csrfToken},
                        success: function (result) {
                            if (result.success === false) {
                                toastr.error(result.message);
                                return;
                            }
                            toastr.success(result.message);
                            getInventoryItems();
                        }
                    });
                }
            });
        }

        function getInventoryItems() {
            $('#inventoryItems').html(
                '<div class="erp-chart__loading"><div class="spinner-border spinner-border-sm text-success" role="status"></div><span>Loading inventory…</span></div>'
            );
            $.ajax({
                url: "/inventory-items/",
                headers: {'X-CSRF-TOKEN': csrfToken},
                success: function (result) {
                    $('#inventoryItems').html(result);
                    setValue(newItemNameInput, '');
                    setValue(newItemTypeInput, '');
                    setValue(itemIdInput, '');
                    setValue(itemNameInput, '');
                    setValue(itemTypeInput, '');
                    setValue(groupNameInput, '');
                    setValue(accountTypeInput, '');
                    setValue(itemUnitInput, '');
                    setValue(itemStatusInput, '');
                    setValue(itemPriceInput, 0);
                    itemNameInput.prop('disabled', false);

                    makeHidden(addNewDiv);
                    makeHidden(additionalInfoDiv);
                    makeHidden(addButton);
                    makeHidden(updateButton);
                    makeHidden(saveButton);
                    makeHidden(deleteButton);

                    filterChartTree($('#coiTreeSearch').val());
                },
                error: function () {
                    $('#inventoryItems').html('<div class="erp-chart__loading">Could not load inventory.</div>');
                }
            });
        }

        newItemTypeInput.on('change', function () {
            if ($(this).val() === 'item') {
                makeVisible(additionalInfoDiv);
            } else {
                makeHidden(additionalInfoDiv);
            }
        });
    </script>
@endpush
