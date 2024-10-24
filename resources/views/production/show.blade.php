@extends('layouts.app')
@section('title', 'Production')
@section('content')
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Production'=>route('productions.index'),
            'Production Details'=>''
            ]
        @endphp
        <x-breadcrumb title='Production Details' :links="$links"/>
            <!-- Basic Inputs start -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h4 class="card-title">Production Details</h4>
                                    <div class="text-right">
                                        <a href="{{ route('production.pdf',encrypt($production->id)) }}" class="btn btn-primary" target="_blank">
                                            <i class="fa fa-download"></i> PDF</a>
                                        @include('buttons.back', [
                                            'route' => route('productions.index'),
                                        ])
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped" width="100%">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: left; padding:8px;" width="50%">
                                                  <p><b>Production No : </b> {{ $production->uid }} </p>
                                                  <p><b>Batch No : </b> {{ $production->batch->batch_no }} </p>
                                                  <p><b>Date : </b> {{ $production->created_at->format('Y-m-d') }} </p>
                                                </td>
                                                <td style="text-align: left; padding:8px;" width="50%">
                                                    <p><b>Status : </b> {!! showStatus($production->status) !!} </p>
                                                    <p><b>Description : </b> {{ $production->remark }} </p>
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
                                                <th>Group</th>
                                                <th>Item</th>
                                                <th>Unit</th>
                                                <th>Quantity</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                            </tr>
                                            @forelse ($production->items as $row)
                                                <tr>
                                                    <td>
                                                        <b>{{ $loop->iteration }}</b>
                                                    </td>
                                                    <td>
                                                        <b>{{ $row->coi->parent ? $row->coi->parent->name : '' }} </b>
                                                    </td>
                                                    <td>
                                                        <b>{{ $row->coi->name ?? ''}} </b>
                                                    </td>
                                                    <td>
                                                        <b>{{ $row->coi->unit ? $row->coi->unit->name : '' }} </b>
                                                    </td>
    
                                                    <td>
                                                        <b>{{ $row->quantity }} </b>
                                                    </td>
                                                    <td>
                                                        <b>{{ $row->rate }} </b>
                                                    </td>
                                                    <td>
                                                        <b>{{ $row->rate * $row->quantity }} </b>
                                                    </td>
                                                </tr>
                                            @empty
    
                                            @endforelse
                                            <tr>
                                                <td colspan="7" style="background-color: #eee;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td rowspan="5" colspan="5">
                                                    <b>Description: </b>{{$production->remark}}
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
