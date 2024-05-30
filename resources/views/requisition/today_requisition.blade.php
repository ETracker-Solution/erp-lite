@extends('layouts.app')
@section('title')
FG Requisition Details
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Requisition Details'=>''
        ]
    @endphp
<x-breadcrumb title='FG Requisition Details' :links="$links"/>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">FG Requisition Details</h3>
                        <a href="{{route('requisition.pdf',encrypt($requisition->id))}}"
                            class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i> PDF</a>
                    </div>
                    <!-- Main content -->
                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Group</th>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th>Quantity</th>
                                        <th>Rate</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-8">

                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Subtotal:</th>
                                        <td>{{ $requisition->subtotal }} TK</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
                <!-- /.invoice -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->
@endsection
@push('style')
    <style>
        .loading-image {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 10;
            width: 200px;
            height: 200px;
        }

        .loader {
            display: none;
            width: 200px;
            height: 200px;
            position: fixed;
            top: 50%;
            left: 50%;
            text-align: center;
            margin-left: -50px;
            margin-top: -100px;
            z-index: 2;
            /*overflow: auto;*/
        }
    </style>
    <style>
        .table_sticky {
            overflow: auto;
            width: 100%;
            height: 200px;
        }

        td,
        th {
            border: 1px solid #000;
            width: 100px;
        }

        th {
            background-color: #c7c7c7;
            font-weight: 700;
        }

        table {
            table-layout: fixed;
            width: 100%;
        }

        td:first-child, th:first-child {
            position: sticky;
            left: 0;
            z-index: 1;
            /*background-color: grey;*/
        }

        td:last-child, th:last-child {
            position: sticky;
            right: 0;
            z-index: 1;
            background-color: #aaaaff;
        }

        thead tr th {
            position: sticky;
            top: 0;
        }

        th:first-child, th:last-child {
            z-index: 2;
            /*background-color: red;*/
        }
    </style>
@endpush
