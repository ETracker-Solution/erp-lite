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
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card card-congratulation-medal">
                    <div class="card-body">
                        <h5 class="text-info">Congratulations Outlet!</h5>
                        <p class="card-text font-small-3">Today's Sales Overview</p>
                        <h1 class="mb-75 mt-2 pt-50">
                            <a href="javascript:void(0);">{{ $todaySale }} BDT</a>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-12">
                <div class="card card-congratulation-medal">
                    <div class="card-body">
                        <a href="{{route('fg-delivery-receives.create')}}" class="btn btn-sm btn-block btn-info">
                            Receivable Delivery
                            <h1>
                                {{ $requisition_deliveries_count }}
                            </h1>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-12">
                <div class="card card-congratulation-medal">
                    <div class="card-body">
                        <a href="{{route('sales-deliveries.index')}}" class="btn btn-sm btn-block btn-info">
                            OOS delivery
                            <h1>
                                {{ $otherOutletSales }}
                            </h1>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-12">
                <div class="card card-congratulation-medal">
                    <div class="card-body">
                        <a href="javascript:void(0)" class="btn btn-sm btn-block btn-info">
                            PETTY CASH
                            <h1>
                                {{ $outletPettyCashAmount }}
                            </h1>
                        </a>
                    </div>
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

                                <x-card-statistics title="Total Stock" value="{{ number_format($stock['total'] ) .' TK.' }}" icon="layers"
                                                   colorClass="bg-light-primary"/>
                            </div>
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
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Statistics Card -->
        </div>
        <div class="row match-height">
            <div class="col-lg-4 col-12">
                <div class="row match-height">

                    <!-- Earnings Card -->
                    <div class="col-lg-12 col-md-6 col-12">
                        <div class="card earnings-card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Total Discount</h3>
                                <div class="card-tools">
                                    Today's Report
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="mb-1">{{ $discount['thisDay'] }}</h5>
                                    </div>
                                    <div class="col-6">
                                        <canvas id="discount-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Earnings Card -->
                </div>
            </div>

            <!-- Revenue Report Card -->
            <div class="col-lg-8 col-12">
                <div class="card card-revenue-budget">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Total Stock : {{ $stock['total'] . 'TK.' }}</h3>
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
            <div class="col-xl-12 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Latest 5 Invoices</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>INV No</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($latestFiveSales as $row)
                                <tr>
                                    <td>{{ $row->invoice_number }}</td>
                                    <td>{{ $row->grand_total}}</td>
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
        <div class="row match-height">
            {{--            <div class="col-xl-4 col-12">--}}
            {{--                <div class="card">--}}
            {{--                    <div class="card-header">--}}
            {{--                        <h2 class="fw-bolder mb-0">Total Customer</h2>--}}
            {{--                    </div>--}}
            {{--                    <div class="card-body">--}}
            {{--                        <div class="row avg-sessions pt-50">--}}
            {{--                            <div class="col-12">--}}
            {{--                                <div class="progress progress-bar-warning" style="height: 12px">--}}
            {{--                                    <div class="progress-bar" role="progressbar" aria-valuenow="50"--}}
            {{--                                         aria-valuemin="50" aria-valuemax="100" style="width: 50%"></div>--}}
            {{--                                </div>--}}
            {{--                                <p class="mb-50">Pending 1000</p>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                        <div class="row avg-sessions pt-50">--}}
            {{--                            <div class="col-12">--}}
            {{--                                <div class="progress progress-bar-primary" style="height: 12px">--}}
            {{--                                    <div class="progress-bar" role="progressbar" aria-valuenow="25"--}}
            {{--                                         aria-valuemin="25" aria-valuemax="100" style="width: 50%"></div>--}}
            {{--                                </div>--}}
            {{--                                <p class="mb-50">Processing 1000</p>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                        <div class="row avg-sessions pt-50">--}}
            {{--                            <div class="col-12">--}}
            {{--                                <div class="progress progress-bar-success" style="height: 12px">--}}
            {{--                                    <div class="progress-bar" role="progressbar" aria-valuenow="60"--}}
            {{--                                         aria-valuemin="60" aria-valuemax="100" style="width: 50%"></div>--}}
            {{--                                </div>--}}
            {{--                                <p class="mb-50">Approved 1000</p>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}

        </div>
        <div class="row match-height">
            <div class="col-xl-12 col-12">
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
                                    <td>{{ $customer->name }}</td>
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
        </div>
        <div class="row match-height">
            <div class="col-xl-8 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Best Selling Products</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="best-selling-product-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-12">
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
            {{-- <div class="col-xl-6 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Sales Comparison</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="sales-comparison-chart"></canvas>
                    </div>
                </div>
            </div> --}}
            <div class="col-xl-12 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Wastage Comparison</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="sales-and-wastage-comparison-chart"></canvas>
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
    <script>


        new Chart("ourOverChart", {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: 'Sales',
                    data: [730, 759, 80, 81, 56, 5, 40],
                    fill: false,
                    borderColor: 'rgb(10,42,42)',
                    tension: 0.1
                },
                    {
                        label: 'Purchases',
                        data: [710, 575, 70, 91, 66, 55, 30],
                        fill: false,
                        borderColor: 'rgb(134,229,229)',
                        tension: 0.1
                    },
                    {
                        label: 'Expenses',
                        data: [512, 40, 60, 50, 22, 33, 65],
                        fill: false,
                        borderColor: 'rgb(224,177,177)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                title: {
                    display: true,
                    text: "Business Analytics Summary"
                },
                legend: {display: true},
                scales: {
                    yAxes: [{ticks: {min: 0, max: 1000}}],
                }
            }
        });
    </script>
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
                    data: JSON.parse('<?= json_encode($stock['productWise']['stock']) ?>'),
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
        const xValues = ["1-02-2024", "2-02-2024", "3-02-2024", "4-02-2024", "5-02-2024", "6-02-2024", "7-02-2024", "8-02-2024", "9-02-2024", "10-02-2024", "11-02-2024"];
        const yValues = [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15];

        new Chart("myLineChart", {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,1.0)",
                    borderColor: "rgba(0,0,255,0.1)",
                    data: yValues
                }]
            },
            options: {
                responsive: true,
                legend: {display: false},
                scales: {
                    yAxes: [{ticks: {min: 6, max: 16}}],
                }
            }
        });
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
        var $statisticsOrderChart = document.querySelector('#statistics-order-chart');
        var statisticsOrderChartOptions;
        var statisticsOrderChart;
        statisticsOrderChartOptions = {
            chart: {
                height: 70,
                type: 'bar',
                stacked: true,
                toolbar: {
                    show: false
                }
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0,
                    top: -15,
                    bottom: -15
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '20%',
                    startingShape: 'rounded',
                    colors: {
                        backgroundBarColors: ["#b91d47",
                            "#00aba9",
                            "#2b5797",
                            "#e8c3b9",
                            "#1e7145"],
                        backgroundBarRadius: 5
                    }
                }
            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            colors: [window.colors.solid.warning],
            series: [
                {
                    name: '2020',
                    data: [45, 85, 65, 45, 65]
                }
            ],
            xaxis: {
                labels: {
                    show: false
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                show: false
            },
            tooltip: {
                x: {
                    show: false
                }
            }
        };
        statisticsOrderChart = new ApexCharts($statisticsOrderChart, statisticsOrderChartOptions);
        statisticsOrderChart.render();
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
    <script>
        new Chart("sales-and-wastage-comparison-chart", {
            type: 'horizontalBar',
            data: {
                labels: JSON.parse('<?= json_encode($salesWastageCompare['sales'] ?? 0) ?>'),
                datasets: [{
                    data: JSON.parse('<?= json_encode($salesWastageCompare['wastage'] ?? 0) ?>'),
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

{{--    <script>--}}
{{--        setInterval(function() {--}}
{{--            location.reload(); // This will reload the entire page--}}
{{--        }, 60000); // 60000 milliseconds = 60 seconds--}}
{{--    </script>--}}
@endpush
