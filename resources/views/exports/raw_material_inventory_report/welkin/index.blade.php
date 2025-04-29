@extends('layouts.app')
@section('title')
Stock Report
@endsection
@section('content')
@php
    $links = [
        'Home' => route('dashboard'),
        'Stock Report' => '',
    ];
@endphp
<x-breadcrumb title='Stock Report' :links="$links" />

<section class="content">
    <div class="container-fluid" id="vue_app">
        <form action="{{ route('stock.report.generate') }}" method="GET" id="submitForm" target="_blank">
            @csrf
            <input type="hidden" name="report_type">
            <div class="row">

                <div class="col-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Select Parameters</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="rootAccountType">Inventory Type</label>
                                        <select name="rootAccountType" id="rootAccountType" class="form-control"
                                            v-model="rootAccountType" @change="fetch_store_by_type">
                                            <option value="">Select a Type</option>
                                            <option value="RM">Raw Materials</option>
                                            <option value="FG">Finished Goods</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="store_id">Store</label>
                                        <select name="store_id" id="store_id" class="form-control" v-model="store_id">
                                            <option value="">Select a Store</option>
                                            <option :value="row . id" v-for="row in stores" v-html="row.name">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="store_id">Start Date</label>
                                        <input type="date" name="from_date" id="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="store_id">End Date</label>
                                        <input type="date" name="end_date" id="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="radio" id="all" name="stock_type" value="">
                                        <label for="all">All</label><br>
                                        <input type="radio" id="zero" name="stock_type" value="zero">
                                        <label for="zero">Zero</label><br>
                                        <input type="radio" id="non_zero" name="stock_type" value="non_zero">
                                        <label for="non_zero">Non Zero</label><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Stock Report</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton"
                                            data-type="only_closing_pdf">
                                            Only Closing Balance PDF
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton"
                                            data-type="only_closing_excel">
                                            Only Closing Balance EXCEL
                                        </button>
                                        <br>
                                        <br>
                                        <br>

                                        <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton"
                                            data-type="in_out_pdf">
                                            With Inward Outward PDF
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton"
                                            data-type="in_out_excel">
                                            With Inward Outward EXCEL
                                        </button>
                                        <br>
                                        <br>
                                        <br>

                                        <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton"
                                            data-type="open_close_pdf">
                                            Opening & Closing Balance PDF
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton"
                                            data-type="open_close_excel">
                                            Opening & Closing Balance EXCEL
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
@push('style')
    <style>
        .categoryLoader {
            position: absolute;
            top: 50%;
            right: 40%;
            transform: translate(-50%, -50%);
            color: red;
            z-index: 999;
        }

        input[placeholder="Select date"] {
            display: block;
            width: 100%;
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            box-shadow: inset 0 0 0 transparent;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
    </style>
@endpush
@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="https://cms.diu.ac/vue/vuejs-datepicker.min.js"></script>
    <script>
        $(document).on('click', '.reportButton', function () {
            $('input[name=report_type]').val($(this).attr('data-type'))
            $('#submitForm').submit()
        });

        $(document).ready(function () {
            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        get_stores_by_type_url: "{{ url('fetch-stores-info') }}",
                    },
                    date: new Date(),
                    selectedStore: '',
                    rootAccountType: '',
                    store_id: '',
                    stores: []
                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                },
                methods: {
                    fetch_store_by_type() {
                        var vm = this;
                        var selectedType = vm.rootAccountType;
                        console.log(selectedType);

                        if (selectedType) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_stores_by_type_url + '/' + selectedType)
                                .then(function (response) {
                                    console.log(response.data.stores);
                                    vm.stores = response.data.stores;
                                    vm.pageLoading = false;
                                })
                                .catch(function (error) {
                                    toastr.error('Failed to fetch stores', {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                    vm.pageLoading = false;
                                });
                        }
                    },
                },

                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
                mounted() {

                }


            });

            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });

        });
    </script>
@endpush
