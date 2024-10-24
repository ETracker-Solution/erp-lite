@extends('layouts.app')
@section('title')
    Outlet Payment
@endsection
@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'System Admin Module'=>'',
        'System Config'=>'',
        'Outlet Payment'=>''
        ]
    @endphp
    <x-breadcrumb title='Outlet Payment' :links="$links"/>



    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{route('outlet-configs.store')}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        {{-- @method('put') --}}
                        <!-- Horizontal Form -->
                        <div class="card card-info">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                                        <label for="name">Outlets</label>
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Outlet</th>
                                                <th>Cash Account</th>
                                                <th>Bkash Account</th>
                                                <th>Nagad Account</th>
                                                <th>DBBL Account</th>
                                                <th>UCB Account</th>
                                                <th>Rocket Account</th>
                                                <th>Upay Account</th>
                                            </tr>
                                            </thead>
                                            @foreach ($outlets as $row)
                                                @php
                                                    $bkashConfig =
                                                    \App\Models\OutletTransactionConfig::where(['outlet_id'=>$row->id,'type'=>'Bkash'])->first();
                                                    $cashConfig =
                                                    \App\Models\OutletTransactionConfig::where(['outlet_id'=>$row->id,'type'=>'Cash'])->first();
                                                    $nagadConfig =
                                                    \App\Models\OutletTransactionConfig::where(['outlet_id'=>$row->id,'type'=>'Nagad'])->first();
                                                    $dbblConfig =
                                                    \App\Models\OutletTransactionConfig::where(['outlet_id'=>$row->id,'type'=>'DBBL'])->first();
                                                    $ucbConfig =
                                                    \App\Models\OutletTransactionConfig::where(['outlet_id'=>$row->id,'type'=>'UCB'])->first();
                                                    $rocketConfig =
                                                    \App\Models\OutletTransactionConfig::where(['outlet_id'=>$row->id,'type'=>'Rocket'])->first();
                                                    $upayConfig =
                                                    \App\Models\OutletTransactionConfig::where(['outlet_id'=>$row->id,'type'=>'Upay'])->first();
                                                    $bkash = $bkashConfig ? $bkashConfig->coa_id : null;
                                                    $cash = $cashConfig ? $cashConfig->coa_id : null;
                                                    $nagad = $nagadConfig ? $nagadConfig->coa_id : null;
                                                    $dbbl = $dbblConfig ? $dbblConfig->coa_id : null;
                                                    $ucb = $ucbConfig ? $ucbConfig->coa_id : null;
                                                    $rocket = $rocketConfig ? $rocketConfig->coa_id : null;
                                                    $upay = $upayConfig ? $upayConfig->coa_id : null;
                                                @endphp
                                                <tr>
                                                    <td>{{ $row->name }}</td>
                                                    <td>
                                                        <select name="settings[{{ $row->id }}][Cash]" id=""
                                                                class="form-control">
                                                            @foreach(getAllLedgers() as $account)

                                                                <option value="{{ $account->id }}" {{ $account->id == $cash ?
                                                        'selected' : '' }}>{{ $account->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="settings[{{ $row->id }}][Bkash]" id=""
                                                                class="form-control">
                                                            @foreach(getAllLedgers() as $account)

                                                                <option value="{{ $account->id }}" {{ $account->id == $bkash ?
                                                        'selected' : '' }}>{{ $account->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="settings[{{ $row->id }}][Nagad]" id=""
                                                                class="form-control">
                                                            @foreach(getAllLedgers() as $account)

                                                                <option value="{{ $account->id }}" {{ $account->id == $nagad ?
                                                        'selected' : '' }}>{{ $account->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="settings[{{ $row->id }}][DBBL]" id=""
                                                                class="form-control">
                                                            @foreach(getAllLedgers() as $account)

                                                                <option value="{{ $account->id }}" {{ $account->id == $dbbl ?
                                                        'selected' : '' }}>{{ $account->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="settings[{{ $row->id }}][UCB]" id=""
                                                                class="form-control">
                                                            @foreach(getAllLedgers() as $account)

                                                                <option value="{{ $account->id }}" {{ $account->id == $ucb ?
                                                        'selected' : '' }}>{{ $account->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="settings[{{ $row->id }}][Rocket]" id=""
                                                                class="form-control">
                                                            @foreach(getAllLedgers() as $account)

                                                                <option value="{{ $account->id }}" {{ $account->id == $rocket ?
                                                        'selected' : '' }}>{{ $account->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="settings[{{ $row->id }}][Upay]" id=""
                                                                class="form-control">
                                                            @foreach(getAllLedgers() as $account)

                                                                <option value="{{ $account->id }}" {{ $account->id == $upay ?
                                                        'selected' : '' }}>{{ $account->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </table>


                                    </div>

                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-info float-right"><i class="fa fa-check" aria-hidden="true"></i>
                                    Update
                                </button>
                            </div>
                        </div>
                        <!-- /.card -->
                    </form>
                </div>
                <div class="col-2"></div>
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@push('js')
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
<script>
    $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

        })
</script>

@endpush
