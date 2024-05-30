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
                        <h5>Welcome to the Dashboard !🎉 </h5>
                        <p class="card-text font-small-3">Complete Requisition in the Month</p>
                        <h3 class="mb-75 mt-2 pt-50">
                            {{ $monthlyTotalRequisitions ??0 }}
                        </h3>
                        <a href="{{route('rm-requisitions.index')}}">
                            <button type="button" class="btn btn-primary">View Requisition</button>
                        </a>
                        <img
                            src="https://s3-alpha-sig.figma.com/img/06e9/a838/9504967120f836bf7c1a80eaeb742559?Expires=1711929600&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4&Signature=eHuLIto9PXADswFjFMxd3hhyQZHSwpGI58bMcNpk9jLpi9ywti9C4Tg12eejR49ijeljNCNyxsj0y6HCo0BzKnb~AUAtz63lBhlGDrpqjeVoNMsTOcIhwbkvjiPAa8O6M15HqPoXlZKKWplNlgnmw6ddp1hwUlH~VeQ50Vez9duv0Za9IBUOxYTMTHXQRMkyHShlA55urqI5c08GxUH9rCdiMndGD4Bw94ECU40TwcPNthXtjsfQuvKhoqq7eGJl-CD3Ol-p1URM66D2DfOgEBPfZ7Dj7PlGkV1gJ-NNlnT5KEe4h1Qo-0gS5Y~2KwfWdSro85n9i3NGtnMbO5qUNQ__"
                            class="congratulation-medal" alt="Medal Pic"
                            style="height: 200px; transform: rotateY(180deg)"/>
                    </div>
                </div>
            </div>
            <!--/ Medal Card -->

            <!-- Statistics Card -->
            <div class="col-xl-8 col-md-6 col-12">
                <div class="card card-statistics">
                    <div class="card-header">
                        <h4 class="card-title">Statistics</h4>
                        <div class="d-flex align-items-center">
                            <p class="card-text font-small-2 mr-25 mb-0">Updated 1 minute ago</p>
                        </div>
                    </div>
                    <div class="card-body statistics-body">
                        <div class="row">
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                <x-card-statistics title="Total Stock" value="{{ $todayTotalStocks }}" icon="layers"
                                                   colorClass="bg-light-primary"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                <x-card-statistics title="Requisition" value="{{ $todayTotalRequisitions }}"
                                                   icon="layers"
                                                   colorClass="bg-light-info"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                <x-card-statistics title="Delivery" value="{{ $todayTotalDeliveries }}"
                                                   icon="shopping-cart"
                                                   colorClass="bg-light-danger"/>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12">
                                <x-card-statistics title="Wastage" value="{{ $todayTotalWastages }}" icon="trash-2"
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
                            <div class="card-body pb-50">
                                <h3 class="font-weight-bolder mb-1">{{ $currentMonthExpense }} </h3>
                                <p class="text-muted">Expenses</p>
                                <div id="expense-radial-bar-chart" class="my-2"></div>
                                <p>{{ $expenseMessage }}</p>
                            </div>
                        </div>
                    </div>
                    <!--/ Bar Chart - Orders -->

                    <!-- Line Chart - Profit -->
                    <div class="col-lg-6 col-md-3 col-6">
                        <div class="card card-tiny-line-stats">
                            <div class="card-body pb-50">
                                <h3 class="font-weight-bolder mb-1">Wastage</h3>
                                <p class="text-muted">This Month</p>
                                <div id="statistics-profit-chart"></div>
                                <p>{{$thisMonthTotalWastages}}</p>
                            </div>
                        </div>
                    </div>
                    <!--/ Line Chart - Profit -->

                    <div class="col-xl-12 col-12">
                        <div class="card">
                            <div
                                class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                                <div class="header-left">
                                    <h4 class="card-title">Delivery</h4>
                                    <p>{{$monthlyTotalDeliveries}}</p>
                                </div>
                                <div class="header-right d-flex align-items-center mt-sm-0 mt-1">
                                    {{--                            <i data-feather="calendar"></i>--}}
                                    {{--                            <input type="text" class="form-control flat-picker border-0 shadow-none bg-transparent pr-0" placeholder="YYYY-MM-DD" />--}}
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="best-selling-product-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Report Card -->
            <div class="col-lg-8 col-12">
                <div class="card card-revenue-budget">
                    <div class="row mx-0">
                        <div class="col-md-12 col-12 revenue-report-wrapper">
                            <div class="d-sm-flex justify-content-between align-items-center mb-3">
                                <div class="mb-sm-0">
                                    <h4 class="card-title mb-sm-0">Total Stock</h4>
                                    <p class="mb-50 mb-sm-0">Raw Stock Summary</p>
                                </div>
                            </div>
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Revenue Report Card -->
        </div>
        <div class="row match-height">
            <div class="col-xl-8 col-12">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                        <div class="header-left">
                            <h4 class="card-title">Total Stock</h4>
                            <p>Monthly Stock Overview</p>
                        </div>
                        <div class="header-right d-flex align-items-center mt-sm-0 mt-1">
                            {{--                            <i data-feather="calendar"></i>--}}
                            {{--                            <input type="text" class="form-control flat-picker border-0 shadow-none bg-transparent pr-0" placeholder="YYYY-MM-DD" />--}}
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="total-expense-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-12">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                        <div class="header-left">
                            <h4 class="card-title">Total Wastage</h4>
                            {{-- <p>Total {{$monthlyWastages->sum('total')}} wastage in this month</p> --}}
                        </div>
                        <div class="header-right d-flex align-items-center mt-sm-0 mt-1">
                            {{--                            <i data-feather="calendar"></i>--}}
                            {{--                            <input type="text" class="form-control flat-picker border-0 shadow-none bg-transparent pr-0" placeholder="YYYY-MM-DD" />--}}
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas height="350" id="sale-purchase-expense-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-xl-4 col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4>
                                Outlet Wise Delivery
                            </h4>
                            {{-- <p>Total {{$monthlyDeliveries->sum('total')}} Delivery in this Month</p> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{-- @foreach($monthlyDeliveries as $row)

                                    <tr>
                                        <td>
                                            <p>{{$row->outlet->name??'Not Found'}}</p>
                                        </td>
                                        <td> {{$row->total??''}}</td>
                                    </tr>
                                @endforeach --}}


                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-xl-8 col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4>
                                Today's Requisition
                            </h4>
                            <p>Total {{$todayRequisitions->count()}} Requisition Today</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Outlet</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    {{-- @dd($todayRequisitions); --}}
                                @foreach($todayRequisitions as $row)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td> {{$row->fromStore->name ?? ''}}</td>
                                        <td>{!! showStatus($row->status) !!}</td>
                                        <td>{{$row->created_at->format('d-m-Y')}}</td>
                                        <td><a target="_blank" href="#" title="View">
                                                <i class="fas fa-eye ml-1"></i>
                                            </a></td>
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
            <div class="col-xl-8 col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4>
                                Outlet Requisition
                            </h4>
                            <p>Total {{$monthlyRequisitions->count()}} Requisition in this Month</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Outlet</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($monthlyRequisitions as $row)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td> {{$row->fromStore->name ?? ''}}</td>
                                        <td>{!! showStatus($row->status) !!}</td>
                                        <td>{{$row->created_at->format('d-m-Y')}}</td>
                                        <td><a target="_blank" href="#" title="View">
                                                <i class="fas fa-eye ml-1"></i>
                                            </a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
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
            labels: JSON.parse('<?= json_encode($dailyRawProductWiseStock['products'] ?? '') ?>'),
            datasets: [
                {
                    label: "Stock Product",
                    backgroundColor: "green",
                    borderColor: "lightgreen",
                    borderWidth: 1,
                    data: JSON.parse('<?= json_encode($dailyRawProductWiseStock['stock'] ?? '') ?>'),
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
            var ctxA = document.getElementById("attendanceChart").getContext("2d");
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
        var ctx = document.getElementById("myChart").getContext("2d");

        var data = {
            labels: ["product-1", "product-2", "product-3", "product-4", "product-5", "product-6", "product-7", "product-8", "product-9", "product-10"],
            datasets: [{
                label: "Stock",
                backgroundColor: "blue",
                data: [100, 85, 80, 86, 70, 60, 30, 17, 14, 10]
            }]
        };

        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                responsiveAnimationDuration: 600,
                cutoutPercentage: 80,
                barValueSpacing: 10,
                plugins: {
                    legend: {
                        position: 'left',
                    },
                    title: {
                        display: true,
                        text: 'Chart.js Bar Chart'
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0,
                        }
                    }]
                },

            }
        });

    </script>
    <script>
        new Chart(document.getElementById("earning-chart"), {
            type: "pie",
            data: {
                labels: ["Italy", "France", "Spain", "USA", "Argentina"],
                datasets: [{
                    backgroundColor: [
                        "#b91d47",
                        "#00aba9",
                        "#2b5797",
                        "#e8c3b9",
                        "#1e7145"
                    ],
                    data: [55, 49, 44, 24, 15]
                }],
                display: false
            },
            options: {
                title: {
                    display: false,
                    text: "World Wide Wine Production 2018"
                },
                legend: {
                    show: false,
                }
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
                    data: JSON.parse("{{json_encode($thisMonthWastages)}}")
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
    <script>
        new Chart("total-expense-chart", {
            type: "bar",
            data: {
                labels: JSON.parse('<?= json_encode($monthlyRawProductWiseStock['products'] ?? '') ?>'),
                datasets: [{
                    backgroundColor: ["red", "green", "blue", "orange", "brown", "red", "green", "blue", "orange", "brown"],
                    data: JSON.parse('<?= json_encode($monthlyRawProductWiseStock['stock'] ?? '') ?>'),
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
    </script>
    <script>
        new Chart("outlet-wise-chart", {
            type: "line",
            data: {
                labels: [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150],
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,1.0)",
                    borderColor: "rgba(0,0,255,0.1)",
                    data: [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15]
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
                labels: JSON.parse('<?= json_encode($outletWiseWastage['outlet']??'Not Found') ?>'),
                datasets: [{
                    backgroundColor: [
                        "#b91d47",
                        "#00aba9",
                        "#2b5797",
                        "#e8c3b9",
                        "#1e7145"
                    ],
                    data: JSON.parse('<?= json_encode($outletWiseWastage['total']??0) ?>'),
                }]
            },
            options: {
                title: {
                    display: false,
                    text: "World Wide Wine Production 2018"
                },
                legend: {
                    display: false
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
    <script>
        new Chart("best-selling-product-chart", {
            type: 'horizontalBar',
            data: {
                labels: ["Delivery", "Requisition"],
                datasets: [{
                    data: [JSON.parse('<?= json_encode($monthlyTotalDeliveries) ?>'), JSON.parse('<?= json_encode($monthlyTotalRequisitions) ?>')],
                    backgroundColor: ["#92F5D5", "#510685"],
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
                            callback: function (label, index, labels) {
                                return label / 1;
                            }

                        },
                        scaleLabel: {
                            display: true,
                            padding: 10,
                            fontFamily: 'Lato',
                            fontColor: '#555759',
                            fontSize: 16,
                            fontStyle: 700,
                            labelString: 'Monthly Report'
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
