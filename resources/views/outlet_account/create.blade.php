@extends('layouts.app')
@section('title')
    Outlet Account
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Master Data'=>'',
            'Account Setting'=>'',
            'Outlet Account' . (isset($outletAccount) ? ' Edit' : ' Entry') => '',
        ];
    @endphp
    <x-breadcrumb title='Outlet Account' :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form
                        @if (isset($outletAccount)) action="{{ route('outlet-accounts.update', encrypt($outletAccount->id)) }}" @else action="{{ route('outlet-accounts.store') }}" @endif
                        method="POST">
                        @csrf
                        @if (isset($outletAccount))
                            @method('PUT')
                        @endif
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">{{ isset($outletAccount) ? 'Edit' : 'Add' }} Outlet Account</h3>
                                <div class="card-tools">
                                    <a href="{{ route('outlet-accounts.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-3 col-md-3 col-12 mb-1">
                                        <x-forms.select label="Outlet" inputName="outlet_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="isset($outletAccount) ? $outletAccount->outlet_id : ''" :options="$outlets" optionId="id" optionValue="name"/>
                                    </div>

                                    @unless(isset($outletAccount))
                                    <div class="col-xl-3 col-md-3 col-12 mb-1">
                                        <label>Mode</label>
                                        <select name="mode" id="mode" class="form-control">
                                            <option value="create">Create new ledger</option>
                                            <option value="link">Link existing ledger</option>
                                        </select>
                                    </div>
                                    @endunless

                                    <div class="col-xl-3 col-md-3 col-12 mb-1 mode-create">
                                        <x-forms.text label="Account Name" inputName="name" placeholder="Enter Account Name"
                                            :isRequired='true' :isReadonly='false' :defaultValue="isset($outletAccount) ? optional($outletAccount->coa)->name : ''" />
                                    </div>

                                    <div class="col-xl-3 col-md-3 col-12 mb-1 mode-create">
                                        <x-forms.select label="Parent Group" inputName="coa_id" placeholder="Select Group" :isRequired='true'  :isReadonly='false' defaultValue="" :options="$chartOfAccounts" optionId="id" optionValue="name"/>
                                    </div>

                                    @unless(isset($outletAccount))
                                    <div class="col-xl-3 col-md-3 col-12 mb-1 mode-link" style="display:none">
                                        <label>Existing Ledger <span class="text-danger">*</span></label>
                                        <select name="existing_coa_id" id="existing_coa_id" class="form-control select2bs4" style="width:100%">
                                            <option value="">Search / select ledger…</option>
                                            @foreach($existingLedgers ?? [] as $ledger)
                                                <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Bank/cash ledgers already in Chart of Accounts.</small>
                                    </div>
                                    @endunless

                                    <div class="col-xl-3 col-md-3 col-12 mb-1">
                                        <x-forms.static-select label="Status" inputName="status" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="isset($outletAccount) ? $outletAccount->status : 'active'" :options="['active','inactive']"/>
                                    </div>
                                    <div class="col-xl-3 col-md-3 col-12 mb-1">
                                        <x-forms.static-select label="Payment Type" inputName="type" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="isset($currentType) ? $currentType : ''" :options="$accountTypes"/>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-info float-right"><i class="fa fa-check" aria-hidden="true"></i>
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js_scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        (function () {
            const $mode = $('#mode');
            const $existing = $('#existing_coa_id');

            function initExistingSelect() {
                if (!$existing.length) return;
                if ($existing.hasClass('select2-hidden-accessible')) {
                    $existing.select2('destroy');
                }
                $existing.select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: 'Search / select ledger…',
                    allowClear: true,
                });
            }

            function syncModeFields() {
                if (!$mode.length) return;
                const isLink = $mode.val() === 'link';

                $('.mode-create').toggle(!isLink);
                $('.mode-link').toggle(isLink);

                // Avoid HTML5 required blocking submit on hidden fields
                $('.mode-create').find('input, select').prop('disabled', isLink);
                $('.mode-link').find('input, select').prop('disabled', !isLink);

                if (isLink) {
                    initExistingSelect();
                }
            }

            $mode.on('change', syncModeFields);
            syncModeFields();
        })();
    </script>
@endpush
