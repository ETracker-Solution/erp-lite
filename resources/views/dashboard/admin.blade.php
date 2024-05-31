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
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card card-congratulation-medal">
                    <div class="card-body">
                        <h5 class="text-info">Congratulations 🎉 Admin!</h5>
                        <p class="card-text font-small-3">Today's Sales Overview</p>
                        <h3 class="mb-75 mt-2 pt-50">
                            <a href="javascript:void(0);">{{ $totalSales }} BDT</a>
                        </h3>
                        <a type="button" class="btn btn-primary" href="{{ route('sales.index') }}">View Sale</a>
                        <img
                            src="https://s3-alpha-sig.figma.com/img/546c/5a37/4c62faf89e51f8af3d964aefcde244b8?Expires=1711929600&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4&Signature=ZRnXfOLrptLQPS7zGcE~1WlhQu~ZynyRud0XimsBQrnAuUsMrVAafCC114I4xF6J7tL-N1thpB8CzZupobQ8KR8sN5bmd0evO9kZz16BQ-AO-G~5-jF9LSC-zDHcEJ-CAH6IegnAoeVGAPYUhCk4mc1rTk~3MhiCwbw-fJnDizSW-TZ9nZwnWm3oc2pNY6ac~u0q6NRiR1oi~4C0JOxFgZWb5LJAVcryH4Uh2FjHgyJ0H5uDMi0BH3pmB3YRCAPETM5F2fSyIqWqR5agcanHMPLKq8x4dx4zjxvexyZklARJ39G7ux2SenCLJ5pjALZDLYr-v8m00wvYGHTLQvZe3A__"
                            class="congratulation-medal" alt="Medal Pic"
                            style="height: 200px; transform: rotateY(180deg)"/>
                    </div>
                </div>
            </div>
            <!--/ Medal Card -->

            <!-- Statistics Card -->
            <div class="col-xl-8 col-md-6 col-12">
                <div class="card card-statistics">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Statistics</h4>
                        <div class="card-tools">
                            Updated 1 minute ago
                        </div>
                    </div>
                    <div class="card-body statistics-body">
                        <div class="row">
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                <x-card-statistics title="Outlets" value="{{ $outlets }}" icon="layers"
                                                   colorClass="bg-light-primary"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                <x-card-statistics title="Customers" value="{{ $customers }}" icon="fa fa-user"
                                                   colorClass="bg-light-info"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                <x-card-statistics title="Products" value="{{ $products }}" icon="shopping-cart"
                                                   colorClass="bg-light-danger"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12">
                                <x-card-statistics title="Wastage" value="{{ $wastageAmount }}" icon="trash-2"
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
                    <!-- Bar Chart - Orders -->
                    <div class="col-lg-6 col-md-3 col-6">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">{{ $currentMonthExpense }} BDT</h3>
                                    <div class="card-tools">
                                        Expenses
                                    </div>
                            </div>
                            <div class="card-body pb-50">
                                <div id="expense-radial-bar-chart" class="my-2"></div>
                                <p>{{ $expenseMessage }}</p>
                            </div>
                        </div>
                    </div>
                    <!--/ Bar Chart - Orders -->

                    <!-- Line Chart - Profit -->
                    <div class="col-lg-6 col-md-3 col-6">
                        <div class="card card-tiny-line-stats">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Sales</h3>
                                    <div class="card-tools">
                                        Last Month
                                    </div>
                            </div>
                            <div class="card-body pb-50">
                                <div id="statistics-profit-chart"></div>
                            </div>
                        </div>
                    </div>
                    <!--/ Line Chart - Profit -->

                    <!-- Earnings Card -->
                    <div class="col-lg-12 col-md-6 col-12">
                        <div class="card earnings-card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Total Discount</h3>
                                    <div class="card-tools">
                                        Monthly Report
                                    </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="mb-1">{{ $discount['thisMonth'] }}</h5>
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
            <div class="col-xl-4 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">{{ $expense['total'] }} BDT</h3>
                            <div class="card-tools">
                                Total Expense
                            </div>
                    </div>
                    <div class="card-body">
                        <canvas id="total-expense-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Sales</h3>
                            <div class="card-tools">
                                Total {{ $sales['total'] }} BDT this month
                            </div>
                    </div>
                    <div class="card-body">
                        <canvas id="outlet-wise-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Total Orders</h4>
                        <div class="card-tools">Total {{ $order['total'] }} order this month</div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Outlet</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order['outletWise'] as $key=>$value)
                                <tr>
                                    <td>{{$key}}</td>
                                    <td>{{$value}}</td>
                                </tr>
                            @endforeach
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
            <div class="col-xl-12 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Requistions</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-bordered">
                                {{-- show from datatable --}}
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
            <div class="col-xl-6 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Sales, Purchase, Expense</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="sale-purchase-expense-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-xl-8 col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title">Best Selling Products</h4>
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
                                        <td>{{ $product->category->name }}</td>
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
    {{--    <script src="{{asset('admin/app-assets/js/scripts/pages/dashboard-ecommerce.js')}}"></script>--}}
    <script>
        $(document).ready(function () {
            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('requisitions.index') }}",
                },
                columns: [{
                    data: "DT_RowIndex",
                    title: "SL",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false
                },

                    {
                        data: "uid",
                        title: "FGR No",
                        searchable: true,
                        "defaultContent": "Not Set"
                    }, {
                        data: "date",
                        title: "Date",
                        searchable: true,
                        "defaultContent": "Not Set"
                    },
                    {
                        data: "store.name",
                        title: "Store",
                        searchable: false,
                        "defaultContent": "Not Set"
                    },
                    {
                        data: "status",
                        title: "Status",
                        searchable: false, "defaultContent": "Not Set"
                    },
                    {
                        data: "created_at",
                        title: "Created At",
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

        var barChartDataOrder = {
            labels: JSON.parse('<?= json_encode($stock['productWise']['products']) ?>'),
            datasets: [
                {
                    backgroundColor: "green",
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
    {{--    <script>--}}
    {{--        var ctx = document.getElementById("myChart").getContext("2d");--}}

    {{--        var data = {--}}
    {{--            labels: ["product-1", "product-2", "product-3", "product-4", "product-5", "product-6", "product-7", "product-8", "product-9", "product-10"],--}}
    {{--            datasets: [{--}}
    {{--                label: "Stock",--}}
    {{--                backgroundColor: "blue",--}}
    {{--                data: [100, 85, 80, 86, 70, 60, 30, 17, 14, 10]--}}
    {{--            }]--}}
    {{--        };--}}

    {{--        var myBarChart = new Chart(ctx, {--}}
    {{--            type: 'bar',--}}
    {{--            data: data,--}}
    {{--            options: {--}}
    {{--                responsive: true,--}}
    {{--                maintainAspectRatio: false,--}}
    {{--                responsiveAnimationDuration: 600,--}}
    {{--                cutoutPercentage: 80,--}}
    {{--                barValueSpacing: 10,--}}
    {{--                plugins: {--}}
    {{--                    legend: {--}}
    {{--                        position: 'left',--}}
    {{--                    },--}}
    {{--                    title: {--}}
    {{--                        display: true,--}}
    {{--                        text: 'Chart.js Bar Chart'--}}
    {{--                    }--}}
    {{--                },--}}
    {{--                scales: {--}}
    {{--                    yAxes: [{--}}
    {{--                        ticks: {--}}
    {{--                            min: 0,--}}
    {{--                        }--}}
    {{--                    }]--}}
    {{--                },--}}

    {{--            }--}}
    {{--        });--}}

    {{--    </script>--}}
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
        var $statisticsProfitChart = document.querySelector('#statistics-profit-chart');
        var statisticsProfitChartOptions;
        var statisticsProfitChart;
        statisticsProfitChartOptions = {
            chart: {
                height: 70,
                type: 'line',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            grid: {
                borderColor: '#000000',
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
                padding: {
                    top: -30,
                    bottom: -10
                }
            },
            stroke: {
                width: 3
            },
            colors: [window.colors.solid.info],
            series: [
                {
                    data: JSON.parse("{{json_encode($lastMonthSales)}}")
                }
            ],
            markers: {
                size: 2,
                colors: window.colors.solid.info,
                strokeColors: window.colors.solid.info,
                strokeWidth: 2,
                strokeOpacity: 1,
                strokeDashArray: 0,
                fillOpacity: 1,
                discrete: [
                    {
                        seriesIndex: 0,
                        dataPointIndex: 5,
                        fillColor: '#ffffff',
                        strokeColor: window.colors.solid.info,
                        size: 5
                    }
                ],
                shape: 'circle',
                radius: 2,
                hover: {
                    size: 3
                }
            },
            xaxis: {
                labels: {
                    show: true,
                    style: {
                        fontSize: '0px'
                    }
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
        statisticsProfitChart = new ApexCharts($statisticsProfitChart, statisticsProfitChartOptions);
        statisticsProfitChart.render();
    </script>
    {{-- <script>
        new Chart("total-expense-chart", {
            type: "bar",
            data: {
                labels: JSON.parse('<?= json_encode($expense['outletWise']['outletName']) ?>'),
                datasets: [{
                    backgroundColor: ["red", "green", "blue", "orange", "brown"],
                    data: JSON.parse('<?= json_encode($expense['outletWise']['expense']) ?>')
                }]
            },
            options: {
                legend: {display: false},
                title: {
                    display: false,
                    text: "World Wine Production 2018"
                }
            }
        });
    </script> --}}
    <script>
        new Chart("outlet-wise-chart", {
            type: "line",
            data: {
                labels: JSON.parse('<?= json_encode($sales['outletWise']['date']) ?>'),
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,1.0)",
                    borderColor: "rgba(0,0,255,0.1)",
                    data: JSON.parse('<?= json_encode($sales['outletWise']['sales']) ?>'),
                }]
            },
            options: {
                legend: {display: false},
                scales: {
                    yAxes: [{ticks: {min: 6, max: 16}}],
                }
            }
        });
    </script>
    <script>
        new Chart("sale-purchase-expense-chart", {
            type: "doughnut",
            data: {
                labels: ["Sale - ({{ $todaySale }})", "Purchase - ({{ $todayPurchase }})", "Expense - ({{ $todayExpense }})",],
                datasets: [{
                    backgroundColor: [
                        "#b91d47",
                        "#00aba9",
                        "#2b5797",
                        "#e8c3b9",
                        "#1e7145"
                    ],
                    data: ["{{ $todaySale }}", "{{ $todayPurchase }}", "{{ $todayExpense }}"]
                }]
            },
            options: {
                title: {
                    display: false,
                    text: "World Wide Wine Production 2018"
                },
                legend: {
                    position: 'left'
                }
            }
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
    <script>
        var $goalOverviewChart = document.querySelector('#expense-radial-bar-chart');
        var goalOverviewChartOptions;
        var goalOverviewChart;
        goalOverviewChartOptions = {
            chart: {
                height: 200,
                type: 'radialBar',
                sparkline: {
                    enabled: true
                },
                dropShadow: {
                    enabled: true,
                    blur: 3,
                    left: 1,
                    top: 1,
                    opacity: 0.1
                }
            },
            colors: ['#51e5a8'],
            plotOptions: {
                radialBar: {
                    offsetY: -10,
                    startAngle: -90,
                    endAngle: 90,
                    hollow: {
                        size: '60%'
                    },
                    track: {
                        background: '#ebe9f1',
                        strokeWidth: '30%'
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            color: '#5e5873',
                            fontSize: '1rem',
                            fontWeight: '600',
                            paddingTop: 0
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    shadeIntensity: 0.5,
                    gradientToColors: [window.colors.solid.success],
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100]
                }
            },
            series: ["{{ $expensePercentage }}"],
            stroke: {
                lineCap: 'round'
            },
            grid: {
                padding: {
                    bottom: 30
                }
            }
        };
        goalOverviewChart = new ApexCharts($goalOverviewChart, goalOverviewChartOptions);
        goalOverviewChart.render();
    </script>
@endpush