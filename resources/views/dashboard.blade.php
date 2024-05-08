@extends('layouts.app')
@section('title')
    Dashboard
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        @php
            $links = [
            'Dashboard'=>''
            ]
        @endphp
        <x-bread-crumb-component title='Dashboard' :links="$links" />
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- /.row -->
            <div class="row" id="vue_app">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-body">
                            <!-- THE CALENDAR -->
                            <div class="row">
                                <div class="col-sm-12 col-12">

                                    <div class="text-right mb-2">
                                        <div class="btn-group">
                                            <button class="btn btn-info active" type="button" @click="fetch_today($event)">Today</button>
                                            <button class="btn btn-info" type="button" @click="fetch_month($event)">This Month</button>
                                            <button class="btn btn-info" type="button" @click="fetch_year($event)">This Year</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1">
                                        <i class="fas fa-shopping-cart"></i>
                                    </span>
                                        <div class="info-box-content" v-if="total_purchases =>0">
                                            <!-- <div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div> -->
                                            <span class="info-box-number">
                                            @{{total_purchases}}
                                        </span>
                                            <span class="info-box-text">Total Purchase</span>
                                        </div>
                                        <div class="overlay" v-else><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <!-- /.col -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-shopping-cart"></i></span>

                                        <div class="info-box-content" v-if="total_sales  =>0">

                                            <span class="info-box-number"> @{{total_sales}}</span>
                                            <span class="info-box-text">Total Sales</span>
                                        </div>
                                        <div class="overlay" v-else><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <!-- /.col -->

                                <!-- fix for small devices only -->
                                <div class="clearfix hidden-md-up"></div>

                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-plus"></i></span>

                                        <div class="info-box-content" v-if="total_profits  =>0">

                                            <span class="info-box-number">@{{total_profits }}</span>
                                            <span class="info-box-text">Total Profit</span>
                                        </div>
                                        <div class="overlay" v-else><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <!-- /.col -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-minus"></i></span>

                                        <div class="info-box-content" v-if="total_expenses  =>0">

                                            <span class="info-box-number">@{{total_expenses}}</span> <span class="info-box-text">Total Expense</span>
                                        </div>
                                        <div class="overlay" v-else><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <!-- /.col -->
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-body">
                            <!-- THE CALENDAR -->

                            <div class="row">

                                <div class="col-md-3">
                                    <!-- Info Boxes Style 2 -->
                                    <div class="info-box mb-3 bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-tag"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">Inventory</span>
                                            <span class="info-box-number">5,200</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box mb-3 bg-success">
                                        <span class="info-box-icon"><i class="far fa-heart"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">Mentions</span>
                                            <span class="info-box-number">92,050</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box mb-3 bg-danger">
                                        <span class="info-box-icon"><i class="fas fa-cloud-download-alt"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">Downloads</span>
                                            <span class="info-box-number">114,381</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box mb-3 bg-info">
                                        <span class="info-box-icon"><i class="far fa-comment"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">Direct Messages</span>
                                            <span class="info-box-number">163,921</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                            </div>
                            <!-- fix for small devices only -->
                            <div class="clearfix hidden-md-up"></div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- BAR CHART -->
                <div class="card card-default">
                    <div class="card-header">

                        <h3 class="card-title"><i class="far fa-chart-bar"></i> Product Wise Stock</h3>
                        <div class="card-tools">

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.card -->
            <!-- /.col (LEFT) -->

            <!-- /.col (RIGHT) -->
        </div>
    </section><!-- /.section -->
@endsection
@push('script')
    <!-- ChartJS -->
    <script src="{{ asset('assets/plugins/chart.js/Chart.min.js')}}"></script>

    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {

                        get_today_summery_url: "{{ url('reports/fetch-today-summery') }}",
                        get_year_summery_url: "{{ url('reports/fetch-year-summery') }}",
                        get_month_summery_url: "{{ url('reports/fetch-month-summery') }}",
                        // get_product_info_url: "{{-- url('admin/fetch-product-info') --}}",
                    },

                    total_purchases: 0,
                    total_sales: 0,
                    total_profits: 0,
                    total_expenses: 0,
                },

                methods: {

                    fetch_today(event) {
                        this.active_btn(event);
                        var vm = this;
                        axios.get(this.config.get_today_summery_url).then(function(response) {
                            // console.log(response.data);
                            vm.total_purchases = response.data.total_purchases;
                            vm.total_sales = response.data.total_sales;
                            vm.total_profits = response.data.total_profits;
                            vm.total_expenses = response.data.total_expenses;
                        }).catch(function(error) {

                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });

                            return false;

                        });


                    },

                    fetch_month(event) {
                        var vm = this;
                        this.active_btn(event);
                        axios.get(this.config.get_month_summery_url).then(function(response) {

                            // console.log(response.data);
                            vm.total_purchases = response.data.total_purchases;
                            vm.total_sales = response.data.total_sales;
                            vm.total_profits = response.data.total_profits;
                            vm.total_expenses = response.data.total_expenses;

                        }).catch(function(error) {

                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });

                            return false;

                        });


                    },
                    fetch_year(event) {
                        var vm = this;
                        this.active_btn(event);
                        axios.get(this.config.get_year_summery_url).then(function(response) {

                            // console.log(response.data);
                            vm.total_purchases = response.data.total_purchases;
                            vm.total_sales = response.data.total_sales;
                            vm.total_profits = response.data.total_profits;
                            vm.total_expenses = response.data.total_expenses;

                        }).catch(function(error) {

                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });

                            return false;

                        });


                    },

                    active_btn(event) {
                        if (event != null || event != undefined) {
                            // console.log(event.target);
                            // console.log();
                            $(event.target).siblings('button').removeClass('active');
                            $(event.target).addClass('active');
                        }
                    }
                },

                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
                beforeMount() {
                    this.fetch_today();
                },

            });

            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>

    <script>
        $(function() {
            /* ChartJS
             * -------
             * Here we will create a few charts using ChartJS
             */
            //-------------
            //- BAR CHART -
            //-------------
            var ali = {
                labels: JSON.parse('{!! $products_name !!}'),
                datasets: [{
                    label: 'Stock',
                    backgroundColor: '#106328',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    pointRadius: false,
                    pointColor: 'rgba(210, 214, 222, 1)',
                    pointStrokeColor: '#c1c7d1',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data: JSON.parse('{!! $products_stock !!}')
                }]
            }
            var barChartCanvas = $('#barChart').get(0).getContext('2d')
            var barChartData = jQuery.extend(true, {}, ali)
            var temp0 = ali.datasets[0]
            barChartData.datasets[0] = temp0

            var barChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                datasetFill: false
            }
            var barChart = new Chart(barChartCanvas, {
                type: 'bar',
                data: barChartData,
                options: barChartOptions
            })
            //---------------------
        })
    </script>
@endpush
