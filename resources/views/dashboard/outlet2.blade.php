@extends('layouts.app')
@section('title','Dashboard')
@push('style')
    <link rel="stylesheet" type="text/css"
          href="{{asset('admin/app-assets/vendors/css/charts/apexcharts.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('admin/app-assets/css/pages/dashboard-ecommerce.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('admin/app-assets/css/plugins/charts/chart-apex.css')}}">
@endpush
@section('content')
    <section id="dashboard-ecommerce">
        @php
            $links = [

            ]
        @endphp
        <x-breadcrumb title='Dashboard' :links="$links"/>
    </section>
    <section class="content">
        <div class="row match-height">
            <!-- Medal Card -->
            <div class="col-xl-3 col-md-6 col-12 mb-3">
                <div class="erp-kpi accent">
                    <div class="label">Today's sales</div>
                    <div class="value">{{ number_format($todaySale, 2) }} BDT</div>
                    <div class="hint">This outlet</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-12 mb-3">
                <a href="{{route('fg-delivery-receives.create')}}" class="erp-kpi-link">
                    <div class="erp-kpi">
                        <div class="label">Receivable deliveries</div>
                        <div class="value">{{ $requisition_deliveries_count }}</div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-4 col-12 mb-3">
                <a href="{{route('sales-deliveries.index')}}" class="erp-kpi-link">
                    <div class="erp-kpi">
                        <div class="label">OOS deliveries</div>
                        <div class="value">{{ $otherOutletSales }}</div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-4 col-12 mb-3">
                <div class="erp-kpi">
                    <div class="label">Petty cash</div>
                    <div class="value">{{ $outletPettyCashAmount }}</div>
                </div>
            </div>
            <!--/ Medal Card -->
        </div>
        <div class="row">
            <!-- Statistics Card -->
            <div class="col-xl-12 col-md-12 col-12">
                <div class="card card-statistics">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Statistics</h3>
                        <div class="card-tools">
                            {{-- Updated 1 minute ago --}}
                        </div>
                    </div>
                    <div class="card-body statistics-body">
                        <div class="row">
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                <x-card-statistics title="Today's Invoice" value="{{ $todayInvoice }}" icon="user"
                                                   colorClass="bg-light-info"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                <x-card-statistics title="Total Item" value="{{ $products }}" icon="shopping-cart"
                                                   colorClass="bg-light-danger"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12">
                                <x-card-statistics title="Last Day Wastage Amount" value="{{ number_format($wastageAmount) .' TK.' }}" icon="trash-2"
                                                   colorClass="bg-light-success"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12">
                                <x-card-statistics title="Total Discount Today" value="{{ number_format($totalDiscountToday) .' TK.' }}" icon="trash-2"
                                                   colorClass="bg-light-success"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Statistics Card -->
        </div>
        <div class="row match-height">
            <div class="col-xl-12 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Receivable Delivery</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Delivery No</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($requisition_deliveries as $row)
                                <tr>
                                    <td>{{ $row->uid }}</td>
                                    <td>{!! showStatus( $row->status) !!} </td>
                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('M d Y')}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"> No Data Found</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script src="{{asset('admin/app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/core/app.js')}}"></script>
    {{--    <script src="{{asset('admin/app-assets/js/scripts/pages/dashboard-ecommerce.js')}}"></script>--}}
    <script>
        $(document).ready(function () {
            $('#req-dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('admin.dashboard') }}",
                },
                columns: [
                    {
                        data: "requisition_number",
                        title: "#ID",
                        searchable: true
                    },
                    {
                        data: "type",
                        title: "Req Type",
                        searchable: true
                    },
                    {
                        data: "status",
                        title: "status",
                        searchable: false
                    },
                    {
                        data: "date",
                        title: "Date",
                        searchable: true
                    },
                    {
                        data: "action",
                        title: "Action",
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        })
    </script>

{{--    <script>--}}
{{--        setInterval(function() {--}}
{{--            location.reload(); // This will reload the entire page--}}
{{--        }, 60000); // 60000 milliseconds = 60 seconds--}}
{{--    </script>--}}
@endpush
