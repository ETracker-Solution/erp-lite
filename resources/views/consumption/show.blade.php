@extends('layouts.app')
@section('title', 'Consumption')
@section('content')
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Consumption'=>route('consumptions.index'),
            'Consumption Details'=>''
            ]
        @endphp
        <x-breadcrumb title='Consumption Details' :links="$links"/>
        <!-- Basic Inputs start -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-info">
                            <div class="card-header">
                                <h4 class="card-title">Consumption Details</h4>
                                <div class="text-right">
                                    <a href="{{ route('consumptions.pdf',encrypt($consumption->id)) }}" class="btn btn-primary" target="_blank">
                                        <i class="fa fa-download"></i> PDF</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left; padding:8px;" width="50%">
                                              <p><b>Consumption No : </b> {{ $consumption->serial_no }} </p>
                                              <p><b>Batch No : </b> {{ $consumption->batch->batch_no }} </p>
                                              <p><b>Date : </b> {{ $consumption->created_at->format('Y-m-d') }} </p>
                                            </td>
                                            <td style="text-align: left; padding:8px;" width="50%">
                                                <p><b>Status : </b> {!! showStatus($consumption->status) !!} </p>
                                                <p><b>Description : </b> {{ $consumption->remark }} </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card card-info" v-if="products.length > 0">
                            <div class="card-header">
                                <h4 class="card-title">Items</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr style="background-color: #80808021;">
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                        </tr>
                                        @forelse ($consumption->items as $row)
                                            <tr>
                                                <td>
                                                    <b>{{ $loop->iteration }}</b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->coi->name }} </b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->coi->parent->name }} </b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->coi->unit->name }} </b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->quantity }} </b>
                                                </td>
                                            </tr>
                                        @empty

                                        @endforelse
                                        <tr>
                                            <td colspan="5" style="background-color: #eee;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">
                                                <b>Description: </b>{{$consumption->remark}}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Basic Inputs end -->
@endsection
