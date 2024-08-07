@extends('layouts.app')
@section('title')
    System Configuration
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'System Admin Module'=>'',
       'System Setting'=>'',
       'System Configuration'=>''
        ]
    @endphp
    <x-breadcrumb title='System Configuration' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('system-config.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-journal-effect-tab" data-toggle="pill" href="#custom-tabs-journal-effect" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Journal Effect</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-loyalty-tab" data-toggle="pill" href="#custom-tabs-loyalty" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Loyalty</a>
                                    </li>
{{--                                    <li class="nav-item">--}}
{{--                                        <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false">Messages</a>--}}
{{--                                    </li>--}}
{{--                                    <li class="nav-item">--}}
{{--                                        <a class="nav-link" id="custom-tabs-four-settings-tab" data-toggle="pill" href="#custom-tabs-four-settings" role="tab" aria-controls="custom-tabs-four-settings" aria-selected="false">Settings</a>--}}
{{--                                    </li>--}}
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-four-tabContent">
                                    <div class="tab-pane fade active show" id="custom-tabs-journal-effect" role="tabpanel" aria-labelledby="custom-tabs-journal-effect-tab">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="card card-info">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Goods Purchase Bill (Journal Effect)</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Debit Account</label>
                                                                    <select name="settings[goods_purchase_bill_debit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('goods_purchase_bill_debit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Credit Account</label>
                                                                    <select name="settings[goods_purchase_bill_credit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('goods_purchase_bill_credit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">GL Account Opening Balance (Journal Effect)</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Liability Credit Account</label>
                                                                    <select name="settings[gl_account_liability_credit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('gl_account_liability_credit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Asset Credit Account</label>
                                                                    <select name="settings[gl_account_asset_credit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('gl_account_asset_credit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card card-info">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Raw Material Opening Balance (Journal Effect)</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Debit Account</label>
                                                                    <select name="settings[rm_opening_balance_debit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('rm_opening_balance_debit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Credit Account</label>
                                                                    <select name="settings[rm_opening_balance_credit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('rm_opening_balance_credit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Finish Goods Opening Balance (Journal Effect)</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Debit Account</label>
                                                                    <select name="settings[fg_opening_balance_debit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('fg_opening_balance_debit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Credit Account</label>
                                                                    <select name="settings[fg_opening_balance_credit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('fg_opening_balance_credit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card card-info">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Raw Material Consumption (Journal Effect)</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Debit Account</label>
                                                                    <select name="settings[rm_consumption_debit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('rm_consumption_debit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Credit Account</label>
                                                                    <select name="settings[rm_consumption_credit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('rm_consumption_credit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Finish Goods Production (Journal Effect)</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Debit Account</label>
                                                                    <select name="settings[fg_production_debit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('fg_production_debit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Credit Account</label>
                                                                    <select name="settings[fg_production_credit_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('fg_production_credit_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-9">
                                                <div class="card card-info">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Sales (Journal Effect)</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Account Receivable</label>
                                                                    <select name="settings[sales_account_receivable_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('sales_account_receivable_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Income From Sales</label>
                                                                    <select name="settings[income_from_sales_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('income_from_sales_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">COGS Account</label>
                                                                    <select name="settings[cogs_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('cogs_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">FG Inventory Account</label>
                                                                    <select name="settings[sales_fg_inventory_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('sales_fg_inventory_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Cash Account</label>
                                                                    <select name="settings[sales_cash_account]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('sales_cash_account') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Retained Earning Account</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="">Account</label>
                                                                    <select name="settings[retained_earning]" id=""
                                                                            class="form-control">
                                                                        <option value="">Select Account</option>
                                                                        @foreach(getAllLedgers() as $account)
                                                                            <option
                                                                                value="{{ $account->id }}" {{ $account->id == getSettingValue('retained_earning') ? 'selected' : '' }}>{{ $account->display_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-loyalty" role="tabpanel" aria-labelledby="custom-tabs-loyalty-tab">
                                       <div class="row">
                                           <div class="col-3">
                                               <div class="form-group">
                                                   <label for="">Minimum Purchase Amount</label>
                                                   <input type="number" name="settings[minimum_purchase_amount]" value="{{ getSettingValue('minimum_purchase_amount') }}" class="form-control">
                                               </div>
                                           </div>
                                       </div>
                                    </div>
{{--                                    <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab">--}}
{{--                                        Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.--}}
{{--                                    </div>--}}
{{--                                    <div class="tab-pane fade" id="custom-tabs-four-settings" role="tabpanel" aria-labelledby="custom-tabs-four-settings-tab">--}}
{{--                                        Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.--}}
{{--                                    </div>--}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <button class="btn btn-sm btn-dark">UPDATE</button>
            </form>
        </div>
    </section>
@endsection
@push('script')
    <script>
        $('#employee').on('select2:select', function (e) {
            const user = JSON.parse(e.params.data.id);
            console.log(user)
            $('input[name=employee_id]').val(user.id)
            $('input[name=name]').val(user.name)
            $('input[name=email]').val(user.email)
        })
    </script>
@endpush
