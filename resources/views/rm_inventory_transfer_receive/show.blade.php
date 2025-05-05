@extends('layouts.app')
@section('title')
RM Inventory Transfer Receive Details
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'RM Inventory Transfer Receive Details'=>''
        ]
    @endphp
<x-breadcrumb title='RM Inventory Transfer Receive Details' :links="$links"/>
<script>
    window.onload = function () {
        // Create an iframe element
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none'; // Hide the iframe
        iframe.src = "{{ route('rm-transfer-receive.pdf', encrypt($rmTransferReceive->id),['print'=>true]) }}"; // URL of the PDF

        // Append the iframe to the document body
        document.body.appendChild(iframe);

        // Wait for the iframe to load, then trigger print
        iframe.onload = function () {
            iframe.contentWindow.print();
        };
    };
</script>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">RM Inventory Transfer Receive Details</h3>
                        <a href="{{route('rm-transfer-receive.pdf',encrypt($rmTransferReceive->id))}}"
                            class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i> PDF</a>
                    </div>
                    <!-- Main content -->
                    <div class="invoice p-3 mb-3">
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left; padding:8px; line-height: 0.6">
                                                <p><b>RMITR No :</b> {{ $rmTransferReceive->uid }}</p>
                                                <p><b>Date :</b> {{ $rmTransferReceive->date }} </p>
                                                <p><b>Status :</b> {!! showStatus($rmTransferReceive->status) !!}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">
                                {{-- <td><b>Customer :</b> {{ $rmTransferReceive->customer->name??'Walking Customer'}}</td> --}}
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">

                            </div>
                            <!-- /.col -->
                        </div>

                        <!-- /.row -->

                        <!-- Table row -->
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>From Store</th>
                                            <th>To Store</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rmTransferReceive->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $rmTransferReceive->date }}</td>
                                            <td>{{ $rmTransferReceive->fromStore->name ?? '' }}</td>
                                            <td>{{ $rmTransferReceive->toStore->name ?? '' }}</td>
                                            <td>{{ $item->quantity ?? '' }}</td>
                                            <td>{{ $item->rate ?? '' }} TK</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                        <!-- /.row -->
                    </div>
                </div>
                <!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->
@endsection
