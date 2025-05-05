@extends('layouts.app')
@section('title','Dashboard')
@push('style')
    <link rel="stylesheet" type="text/css"
          href="{{asset('admin/app-assets/vendors/css/charts/apexcharts.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('admin/app-assets/css/pages/dashboard-ecommerce.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('admin/app-assets/css/plugins/charts/chart-apex.css')}}">
    <style>
        #piechart  svg{
            width: 450px !important;
        }
    </style>
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
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card card-congratulation-medal">
                    <div class="card-body">
                        <h5 class="text-info">Congratulations 🎉 Admin!</h5>
                        <p class="card-text font-small-3">Today's Sales Overview</p>
                        <h1 class="mb-75 mt-2 pt-50">
                            <a href="javascript:void(0);">{{ $totalSales }} BDT</a>
                        </h1>
                    </div>
                </div>
            </div>
            <!-- Statistics Card -->
            <div class="col-xl-8 col-md-6 col-12">
                <div class="card card-statistics">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Statistics</h4>
                        <div class="card-tools">
                            {{-- Updated 1 minute ago --}}
                        </div>
                    </div>
                    <div class="card-body statistics-body">
                        <div class="row">
                            <div class="col-xl-4 col-sm-6 col-12 mb-2 mb-xl-0">
                                <x-card-statistics title="Outlets" value="{{ $outlets }}" icon="layers"
                                                   colorClass="bg-light-primary"/>
                            </div>
                            <div class="col-xl-4 col-sm-6 col-12 mb-2 mb-xl-0">
                                <x-card-statistics title="Today's Invoice" value="{{ $todayInvoice }}" icon="layers"
                                                   colorClass="bg-light-primary"/>
                            </div>
                            <div class="col-xl-4 col-sm-6 col-12 mb-2 mb-xl-0">
                                <x-card-statistics title="Customers" value="{{ $customers }}" icon="fa fa-user"
                                                   colorClass="bg-light-info"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-md-12 col-12">
                <div class="card card-statistics">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Statistics</h4>
                        <div class="card-tools">
                            {{-- Updated 1 minute ago --}}
                        </div>
                    </div>
                    <div class="card-body statistics-body">
                        <div class="row">
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                <x-card-statistics title="Products" value="{{ $products }}" icon="shopping-cart"
                                                   colorClass="bg-light-danger"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12">
                                <x-card-statistics title="Wastage" value="{{ $wastageAmount }}" icon="trash-2"
                                                   colorClass="bg-light-success"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                <x-card-statistics title="RM STOCK" value="{{ $rmStock.' TK' }}" icon="shopping-cart"
                                                   colorClass="bg-light-danger"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12">
                                <x-card-statistics title="FG STOCK" value="{{ $fgStock. ' TK' }}" icon="trash-2"
                                                   colorClass="bg-light-success"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Statistics Card -->
        </div>
        <div class="row match-height">
            <!-- Revenue Report Card -->
            <div class="col-lg-12 col-12">
                <div class="card card-revenue-budget">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Total Stock : {{ $stock['total'] }}</h3>
                    </div>
                    <div class="row mx-0">
                        <div class="col-md-12 col-12 revenue-report-wrapper">
                            <canvas id="stock-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Revenue Report Card -->
        </div>
        <div class="row match-height">
            <div class="col-xl-6 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Total Pre Orders</h4>
                        <div class="card-tools">Total {{ $order['total'] }} pre order this month</div>
                    </div>
                    <div class="card-body">
                        <div id="piechart"></div>
                    </div>

                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12">
                <div class="card earnings-card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Total Discount</h3>
                        <div class="card-tools">
                            Today
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h5 class="mb-1">{{ $discount['today'] }}</h5>
                            </div>
                            <div class="col-6">
                                <canvas id="discount-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-xl-12 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Today's Requisition</h4>
                        <div class="card-tools">
                            Total {{$todayRequisitions->count()}} Requisition Today
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>FGR No</th>
                                    <th>Outlet</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    {{-- @dd($todayRequisitions); --}}
                                @foreach($todayRequisitions as $row)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td> {{$row->date}}</td>
                                        <td> {{$row->uid}}</td>
                                        <td> {{$row->fromStore->name ?? ''}}</td>
                                        <td>{!! showStatus($row->status) !!}</td>
                                        <td>{{$row->created_at->format('d-m-Y')}}</td>
                                        <td><a href="{{ route('requisitions.show', encrypt($row->id)) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-xl-6 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Top Customers</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Point</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($customersWithPoint as $customer)
                                <tr>
                                    <td>{{ $customer->name. $customer->id }}</td>
                                    <td>{{ $customer->mobile }}</td>
                                    <td>{{ $customer->membership->point }}</td>
                                    <td>{{ $customer->sales->sum('grand_total') }}</td>
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
            <div class="col-xl-6 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h2 class="card-title">Top 5 Slow Products</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($slowSellingProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->parent->name ?? ''}}</td>
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
        </div>

        {{--        <div class="row match-height">--}}
        {{--            <div class="col-sm-12 col-xl-6 col-12">--}}
        {{--                <div class="card">--}}
        {{--                    <div class="card-body">--}}
        {{--                        <canvas id="ourOverChart"></canvas>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}

    </section>
@endsection
<?php
    $stocks = $stock['productWise']['stock'];
    // Format each number in the stock array
    $formatted_stocks = array_map(function($num) {
        return number_format($num, 0);
    }, $stocks);
?>
@section('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@section('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection

@push('script')
    <script src="{{asset('admin/app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/core/app.js')}}"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        function generateColors(numColors) {
            var colors = ["green", "red", "blue", "orange", "brown", "purple", "yellow", "cyan", "magenta", "lime"];
            var dynamicColors = [];
            for (var i = 0; i < numColors; i++) {
                dynamicColors.push(colors[i % colors.length]);
            }
            return dynamicColors;
        }

        var barChartDataOrder = {
            labels: JSON.parse('<?= json_encode($stock['productWise']['products']) ?>'),
            datasets: [
                {
                    backgroundColor: generateColors(<?= count($stock['productWise']['products']) ?>),
                    borderColor: "lightgreen",
                    borderWidth: 1,
                    data: JSON.parse('<?= json_encode($formatted_stocks) ?>').map(function(value) {
                        return parseFloat(value.replace(/,/g, ''));
                    }),
                },
            ]
        };

        var chartOptionsOrder = {
            responsive: true,
            legend: {
                position: "top"
            },
            title: {
                display: false,
                text: "Best Sale Product"
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }

        window.onload = function () {
            var ctxA = document.getElementById("stock-chart");
            window.myBar = new Chart(ctxA, {
                type: "bar",
                data: barChartDataOrder,
                options: chartOptionsOrder
            });
        };

    </script>
    <script>
        new Chart(document.getElementById("discount-chart"), {
            type: "doughnut",
            data: {
                labels: JSON.parse('<?= json_encode($discount['outletWiseDiscount']['outletName']) ?>'),
                {{--                labels: [JSON.stringify("{{($discount['outletWiseDiscount']['outletName'])}}")],--}}
                datasets: [{
                    backgroundColor: [
                        "#b91d47",
                        "#00aba9",
                        "#2b5797",
                        "#e8c3b9",
                        "#1e7145"
                    ],
                    data: JSON.parse("{{ json_encode($discount['outletWiseDiscount']['discount']) }}")
                }],
            },
            options: {
                legend: {
                    display: false,
                },
                plugins: {
                    plugins: {
                        datalabels: {
                            color: 'white',
                            formatter: (value) => {
                                return value + '%'
                            }
                        },
                    },
                },
            },

        });
    </script>
    <script>
        new Chart("best-selling-product-chart", {
            type: 'horizontalBar',
            data: {
                labels: JSON.parse('<?= json_encode($bestSellingProducts['name'] ?? '') ?>'),
                datasets: [{
                    data: JSON.parse('<?= json_encode($bestSellingProducts['qty'] ?? 0) ?>'),
                    backgroundColor: ["#73BFB8", "#73BFB8", "#73BFB8", "#73BFB8", "#73BFB8", "#73BFB8", "#73BFB8"],
                }]
            },
            options: {
                tooltips: {
                    enabled: false
                },
                responsive: true,
                legend: {
                    display: false,
                    position: 'bottom',
                    fullWidth: true,
                    labels: {
                        boxWidth: 10,
                        padding: 50
                    }
                },
                scales: {
                    yAxes: [{
                        barPercentage: 0.75,
                        gridLines: {
                            display: true,
                            drawTicks: true,
                            drawOnChartArea: false
                        },
                        ticks: {
                            fontColor: '#555759',
                            fontFamily: 'Lato',
                            fontSize: 11
                        }

                    }],
                    xAxes: [{
                        gridLines: {
                            display: true,
                            drawTicks: false,
                            tickMarkLength: 5,
                            drawBorder: false
                        },
                        ticks: {
                            padding: 5,
                            beginAtZero: true,
                            fontColor: '#555759',
                            fontFamily: 'Lato',
                            fontSize: 11,
                            // callback: function (label, index, labels) {
                            //     return label / 1000;
                            // }

                        },
                        scaleLabel: {
                            display: false,
                            padding: 10,
                            fontFamily: 'Lato',
                            fontColor: '#555759',
                            fontSize: 16,
                            fontStyle: 700,
                            labelString: 'Scale Label'
                        },

                    }]
                }
            }
        });
    </script>
    <script type="text/javascript">
        // Load google charts
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        // Draw the chart and set the chart values
        function drawChart() {
            var rawData = <?= json_encode($order['outletWise']) ?>;
            var dataArray = [['Outlet', 'Orders']];
          for (var key in rawData) {
              if (rawData.hasOwnProperty(key)) {
                  dataArray.push([key, rawData[key]]);
              }
          }
            var data = google.visualization.arrayToDataTable(dataArray);

            // Optional; add a title and set the width and height of the chart
            var options = {'title': 'Pre Order', 'width': 550, 'height': 280};

            // Display the chart inside the <div> element with id="piechart"
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>

{{--    <script>--}}
{{--        setInterval(function() {--}}
{{--            location.reload(); // This will reload the entire page--}}
{{--        }, 60000); // 60000 milliseconds = 60 seconds--}}
{{--    </script>--}}


@endpush
