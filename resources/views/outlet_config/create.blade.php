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
                                        @php
                                            $ledgers = getAllLedgers();
                                            $accountTypes = ['Cash', 'Bkash', 'Nagad', 'DBBL', 'UCB', 'Rocket', 'Upay', 'Nexus', 'PBL', 'Due', 'City', 'Prime'];
                                        @endphp
                                        <label for="name">Outlets</label>
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Outlet</th>
                                                @foreach ($accountTypes as $type)
                                                    <th>{{ $type }} Account</th>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($outlets as $outlet)
                                                @php
                                                    $configs = [];
                                                    foreach ($accountTypes as $type) {
                                                        $configs[$type] = \App\Models\OutletTransactionConfig::where([
                                                            'outlet_id' => $outlet->id,
                                                            'type' => $type
                                                        ])->first();
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $outlet->name }}</td>
                                                    @foreach ($accountTypes as $type)
                                                        <td>
                                                            <select name="settings[{{ $outlet->id }}][{{ $type }}]" class="form-control">
                                                                @foreach ($ledgers as $account)
                                                                    <option value="{{ $account->id }}" {{ isset($configs[$type]) && $account->id == $configs[$type]->coa_id ? 'selected' : '' }}>
                                                                        {{ $account->display_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                            </tbody>
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
