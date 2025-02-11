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
    <x-breadcrumb title='Stock Report' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
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
                                            <label for="store_id">Inventory Type</label>
                                            <select name="rootAccountType" id="rootAccountType" class="form-control">
                                                <option value="">Select a Type</option>
                                                <option value="RM">Raw Materials</option>
                                                <option value="FG">Finish Goods</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="store_id">Store</label>
                                            <select name="store_id" id="store_id" class="form-control">
                                                <option value="">Select a Store</option>
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                                @endforeach
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

                                            <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton" data-type="only_closing_pdf">
                                                Only Closing Balance PDF
                                            </button>
                                            <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton" data-type="only_closing_excel">
                                                Only Closing Balance EXCEL
                                            </button>
                                            <br>
                                            <br>
                                            <br>

                                            <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton" data-type="in_out_pdf">
                                                With Inward Outward PDF
                                            </button>
                                            <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton" data-type="in_out_excel">
                                                With Inward Outward EXCEL
                                            </button>
                                            <br>
                                            <br>
                                            <br>

                                            <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton" data-type="open_close_pdf">
                                                Opening & Closing Balance PDF
                                            </button>
                                            <button type="button" class="btn btn-sm btn-dark w-50 mb-2 reportButton" data-type="open_close_excel">
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
    <script>
        $(document).ready(function () {
            $('.reportButton').on('click', function(){
                $('input[name=report_type]').val($(this).attr('data-type'))
                $('#submitForm').submit()
            });
        });
    </script>
@endpush
