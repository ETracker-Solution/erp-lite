@extends('layouts.app')
@section('title')
    Outlet
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Master Data'=>'',
            'Outlet' . (isset($outlet) ? ' Edit' : ' Entry') => '',
        ];
        $selectedTypes = old('account_types', $assignedTypes ?? $defaultAccountTypes ?? defaultOutletAccountTypes());
    @endphp
    <x-breadcrumb title='Outlet' :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form
                        @if (isset($outlet)) action="{{ route('outlets.update', $outlet->id) }}" @else action="{{ route('outlets.store') }}" @endif
                        method="POST" class="" enctype="multipart/form-data">
                        @csrf
                        @if (isset($outlet))
                            @method('PUT')
                        @endif
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">{{ isset($outlet) ? 'Edit Outlet' : 'Create Outlet' }}</h3>
                                <div class="card-tools">
                                    <a href="{{ route('outlets.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="serial_no">Outlet No</label>
                                            <input type="text" class="form-control" id="serial_no" name="serial_no"
                                                value="{{ old('serial_no', isset($outlet) ? $outlet->id : $serial_no) }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                            :isRequired='true' :isReadonly='false' :defaultValue="isset($outlet) ? $outlet->name : ''" />
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <x-forms.text label="Address" inputName="address" placeholder="Enter Address"
                                            :isRequired='true' :isReadonly='false' :defaultValue="isset($outlet) ? $outlet->address : ''" />
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="active" {{ old('status', isset($outlet) ? $outlet->status : '') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', isset($outlet) ? $outlet->status : '') == 'inactive' ? 'selected' : '' }}>In Active</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <label for="petty_cash">Petty Cash Account</label>
                                        <select name="petty_cash" id="petty_cash" class="form-control">
                                            <option value="1" selected>Create / Keep</option>
                                            <option value="0">Skip</option>
                                        </select>
                                    </div>
                                </div>

                                <hr>
                                <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                                    <div>
                                        <h5 class="mb-0">Payment accounts</h5>
                                        <small class="text-muted">Selected methods get a ledger + outlet link + POS config automatically.</small>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-xs btn-outline-secondary" id="selectDefaultAccounts">Defaults</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary" id="selectAllAccounts">All</button>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach(($accountTypes ?? outletAccountTypeOptions()) as $type)
                                        <div class="col-md-3 col-sm-4 col-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                       class="custom-control-input account-type-check"
                                                       id="account_type_{{ $type }}"
                                                       name="account_types[]"
                                                       value="{{ $type }}"
                                                       data-default="{{ in_array($type, $defaultAccountTypes ?? defaultOutletAccountTypes(), true) ? 1 : 0 }}"
                                                       {{ in_array($type, $selectedTypes, true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="account_type_{{ $type }}">{{ $type }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-info float-right"><i class="fa fa-check" aria-hidden="true"></i>
                                    {{ isset($outlet) ? 'Update & Sync Accounts' : 'Create Outlet' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
<script>
    $('#selectAllAccounts').on('click', function () {
        $('.account-type-check').prop('checked', true);
    });
    $('#selectDefaultAccounts').on('click', function () {
        $('.account-type-check').each(function () {
            $(this).prop('checked', $(this).data('default') == 1);
        });
    });
</script>
@endpush
