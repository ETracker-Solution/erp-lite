@extends('layouts.app')
@section('title')
Chart Of Accounts
@endsection
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Master Data' => '',
            'Account Setting' => '',
            'Chart Of Accounts' => '',
        ];
    @endphp
    <x-breadcrumb title='Chart Of Accounts' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row erp-chart">
                <div class="col-lg-4 col-md-5 mb-3">
                    <div class="card card-info erp-chart__panel h-100">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Account tree</h3>
                            <div class="erp-chart__meta mt-1">Select a group or ledger to edit</div>
                        </div>
                        <div class="card-body pt-3">
                            <div class="erp-chart__search mb-3">
                                <input type="search" id="coaTreeSearch" class="form-control"
                                       placeholder="Search accounts…" autocomplete="off">
                            </div>
                            <div id="inventoryItems" class="erp-chart__tree-wrap">
                                <div class="erp-chart__loading">
                                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                                    <span>Loading accounts…</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7 mb-3">
                    <div class="card card-info erp-chart__panel h-100">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Account details</h4>
                            <div class="erp-chart__meta mt-1">View, add under a group, or update a name</div>
                        </div>
                        <div class="card-body">
                            <div class="row erp-chart__fields">
                                <div class="col-md-6 col-12">
                                    <x-forms.text label="Account ID" inputName="item_id" placeholder=""
                                                  :isRequired='false' :isReadonly='true' defaultValue=""/>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.text label="Group / Ledger" inputName="item_type" placeholder=""
                                                  :isRequired='false' :isReadonly='true' defaultValue=""/>
                                </div>
                                <div class="col-12">
                                    <x-forms.text label="Account Name" inputName="item_name"
                                                  placeholder="Enter account name" :isRequired='false'
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
                                    <label>Bank / Cash</label>
                                    <select name="is_bank_cash" class="form-control">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label>Default Type</label>
                                    <select name="default_type" class="form-control">
                                        <option value="">None</option>
                                        <option value="payment_method">Payment Method (outlet / POS)</option>
                                        <option value="office_account">Office Account (FT receive)</option>
                                        <option value="petty_cash">Petty Cash</option>
                                        <option value="accounts_receivable">Accounts Receivable</option>
                                        <option value="accounts_payable">Accounts Payable</option>
                                        <option value="sales">Sales</option>
                                    </select>
                                    <small class="text-muted">
                                        Groups tagged <strong>Payment Method</strong> appear in outlet account sync automatically (no PHP edit).
                                    </small>
                                </div>
                                <div class="col-12" id="paymentMethodFlags" hidden>
                                    <div class="erp-chart__section-label mt-1">Payment method options</div>
                                    <div class="custom-control custom-checkbox mb-1">
                                        <input type="checkbox" class="custom-control-input" id="is_payment_method" name="is_payment_method" value="1">
                                        <label class="custom-control-label" for="is_payment_method">
                                            Use as outlet / POS payment method
                                        </label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-1">
                                        <input type="checkbox" class="custom-control-input" id="provision_everywhere" name="provision_everywhere" value="1">
                                        <label class="custom-control-label" for="provision_everywhere">
                                            Create ledgers for all outlets + office now
                                        </label>
                                    </div>
                                    <div class="pl-3">
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input" id="provision_outlets" name="provision_outlets" value="1" checked>
                                            <label class="custom-control-label" for="provision_outlets">All active outlets</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input" id="provision_office" name="provision_office" value="1" checked>
                                            <label class="custom-control-label" for="provision_office">Office ledger (<em>Method Office</em>)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row erp-chart__fields erp-chart__fields--add mt-2" id="addNewForm" hidden>
                                <div class="col-12">
                                    <div class="erp-chart__section-label">Add under selected group</div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.static-select label="Account Type" inputName="new_item_type"
                                                           placeholder="Select Type" :isRequired='false'
                                                           :isReadonly='false' defaultValue=""
                                                           :options="['group','ledger']"/>
                                </div>
                                <div class="col-md-6 col-12">
                                    <x-forms.text label="Account Name" inputName="new_item_name"
                                                  placeholder="e.g. Shaj" :isRequired='false'
                                                  :isReadonly='false' defaultValue=""/>
                                </div>
                                <div class="col-12" id="newPaymentMethodFlags" hidden>
                                    <div class="alert alert-light border mb-2 py-2">
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input" id="new_is_payment_method" value="1">
                                            <label class="custom-control-label" for="new_is_payment_method">
                                                Payment method group (auto-lists in outlet accounts / sync)
                                            </label>
                                        </div>
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input" id="new_provision_everywhere" value="1">
                                            <label class="custom-control-label" for="new_provision_everywhere">
                                                Also create ledgers for all outlets + office
                                            </label>
                                        </div>
                                        <div class="pl-3">
                                            <div class="custom-control custom-checkbox mb-1">
                                                <input type="checkbox" class="custom-control-input" id="new_provision_outlets" value="1" checked>
                                                <label class="custom-control-label" for="new_provision_outlets">All active outlets</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mb-1">
                                                <input type="checkbox" class="custom-control-input" id="new_provision_office" value="1" checked>
                                                <label class="custom-control-label" for="new_provision_office">Office ledger (<em>Method Office</em>)</label>
                                            </div>
                                        </div>
                                    </div>
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

        .erp-chart__fields--add {
            background: var(--erp-accent-soft, #e7f2ec);
            border: 1px solid var(--erp-line, #e6e0d8);
            border-radius: 12px;
            padding: 12px 8px 4px;
            margin-left: 0;
            margin-right: 0;
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
            getAccounts();

            $('#coaTreeSearch').on('input', function () {
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
        let isBankCashInput = $("select[name=is_bank_cash]");
        let defaultTypeInput = $("select[name=default_type]");
        let newItemNameInput = $("input[name=new_item_name]");
        let newItemTypeInput = $("select[name=new_item_type]");
        let paymentMethodFlags = $('#paymentMethodFlags');
        let newPaymentMethodFlags = $('#newPaymentMethodFlags');

        function syncPaymentMethodUi(itemType, defaultType) {
            const isGroup = itemType === 'group';
            const isPayment = defaultType === 'payment_method' || $('#is_payment_method').is(':checked');
            if (isGroup) {
                makeVisible(paymentMethodFlags);
                $('#is_payment_method').prop('checked', defaultType === 'payment_method');
            } else {
                makeHidden(paymentMethodFlags);
                $('#is_payment_method').prop('checked', false);
                $('#provision_everywhere').prop('checked', false);
            }
            if (isGroup && ($('#is_payment_method').is(':checked') || isPayment)) {
                // keep provision options visible inside flags block
            }
        }

        $('#is_payment_method').on('change', function () {
            if ($(this).is(':checked')) {
                setValue(defaultTypeInput, 'payment_method');
                setValue(isBankCashInput, 'yes');
            } else if (getValue(defaultTypeInput) === 'payment_method') {
                setValue(defaultTypeInput, '');
            }
        });

        defaultTypeInput.on('change', function () {
            if (getValue(itemTypeInput) === 'group' && getValue(defaultTypeInput) === 'payment_method') {
                $('#is_payment_method').prop('checked', true);
                setValue(isBankCashInput, 'yes');
            }
        });

        newItemTypeInput.on('change', function () {
            if ($(this).val() === 'group') {
                makeVisible(newPaymentMethodFlags);
            } else {
                makeHidden(newPaymentMethodFlags);
                $('#new_is_payment_method').prop('checked', false);
                $('#new_provision_everywhere').prop('checked', false);
                $('#new_provision_outlets').prop('checked', true);
                $('#new_provision_office').prop('checked', true);
            }
        });

        function makeHidden(element) {
            element.prop('hidden', true);
        }

        function makeVisible(element) {
            element.prop('hidden', false);
        }

        function setValue(element, value) {
            element.val(value);
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
                $('#inventoryItems .erp-tree-node.is-active > .erp-tree').show();
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
                url: "/coa-details/" + id,
                headers: {'X-CSRF-TOKEN': csrfToken},
                success: function (result) {
                    setValue(itemIdInput, result.item_id);
                    setValue(itemNameInput, result.item_name);
                    setValue(itemTypeInput, result.item_type);
                    setValue(groupNameInput, result.group_name);
                    setValue(accountTypeInput, result.account_type);
                    setValue(isBankCashInput, result.is_bank_cash || 'no');
                    setValue(defaultTypeInput, result.default_type || '');
                    itemNameInput.prop('disabled', false);
                    makeVisible(updateButton);
                    makeVisible(addButton);
                    makeVisible(deleteButton);
                    makeHidden(addNewDiv);
                    makeHidden(saveButton);
                    syncPaymentMethodUi(result.item_type, result.default_type || '');
                    if (result.item_type === 'ledger') {
                        makeHidden(addButton);
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
                        url: "/coa-update/" + getValue(itemIdInput),
                        data: {
                            item_name: getValue(itemNameInput),
                            is_bank_cash: getValue(isBankCashInput),
                            default_type: getValue(defaultTypeInput),
                            is_payment_method: $('#is_payment_method').is(':checked') ? 1 : 0,
                            provision_everywhere: $('#provision_everywhere').is(':checked') ? 1 : 0,
                            provision_outlets: $('#provision_outlets').is(':checked') ? 1 : 0,
                            provision_office: $('#provision_office').is(':checked') ? 1 : 0,
                        },
                        headers: {'X-CSRF-TOKEN': csrfToken},
                        success: function (result) {
                            if (result.success) {
                                toastr.success(result.message);
                                getAccounts();
                            } else {
                                toastr.error(result.message);
                            }
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
            if (getValue(newItemTypeInput) === 'group') {
                makeVisible(newPaymentMethodFlags);
            } else {
                makeHidden(newPaymentMethodFlags);
            }
        }

        function submitItem() {
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Account');
                return;
            }
            if (!getValue(newItemNameInput)) {
                toastr.error('Please Enter Account Name');
                return;
            }
            if (!getValue(newItemTypeInput)) {
                toastr.error('Please Select Account Type');
                return;
            }
            $.ajax({
                method: 'POST',
                url: "/coa-store/" + getValue(itemIdInput),
                data: {
                    item_name: getValue(newItemNameInput),
                    item_type: getValue(newItemTypeInput),
                    is_payment_method: $('#new_is_payment_method').is(':checked') ? 1 : 0,
                    provision_everywhere: $('#new_provision_everywhere').is(':checked') ? 1 : 0,
                    provision_outlets: $('#new_provision_outlets').is(':checked') ? 1 : 0,
                    provision_office: $('#new_provision_office').is(':checked') ? 1 : 0,
                },
                headers: {'X-CSRF-TOKEN': csrfToken},
                success: function (result) {
                    if (result.success) {
                        toastr.success(result.message);
                        getAccounts();
                    } else {
                        toastr.error(result.message);
                    }
                }
            });
        }

        function deleteInvItem() {
            if (!getValue(itemIdInput)) {
                toastr.error('Please Select Account');
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
                        url: "/coa-delete/" + getValue(itemIdInput),
                        headers: {'X-CSRF-TOKEN': csrfToken},
                        success: function (result) {
                            if (result.success) {
                                toastr.success(result.message);
                            } else {
                                toastr.error(result.message);
                            }
                            getAccounts();
                        }
                    });
                }
            });
        }

        function getAccounts() {
            $('#inventoryItems').html(
                '<div class="erp-chart__loading"><div class="spinner-border spinner-border-sm text-success" role="status"></div><span>Loading accounts…</span></div>'
            );
            $.ajax({
                url: "/coa-items/",
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
                    itemNameInput.prop('disabled', false);

                    makeHidden(addNewDiv);
                    makeHidden(addButton);
                    makeHidden(updateButton);
                    makeHidden(saveButton);
                    makeHidden(deleteButton);

                    filterChartTree($('#coaTreeSearch').val());
                },
                error: function () {
                    $('#inventoryItems').html('<div class="erp-chart__loading">Could not load accounts.</div>');
                }
            });
        }
    </script>
@endpush
