@extends('layouts.app')
@section('title', 'Production')
@section('content')
    <div class="content-wrapper">
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Production'=>route('productions.index'),
            'Production Details'=>''
            ]
        @endphp
        <x-bread-crumb-component title='Production Details' :links="$links"/>
        <div class="content-body">
            <!-- Basic Inputs start -->
            <section id="basic-input">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
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
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                    <tr>
                                        <th><strong>Production No</strong></th>
                                        <td>{{ $production->production_no??'' }}</td>
                                        <th><strong>Date</strong></th>
                                        <td>{{ $production->created_at->format('Y-m-d') }}</td>
                                        <th><strong>Status </strong></th>
                                        <td>{!! showStatus($production->status) !!}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                {{-- <p class="mt-1"><b>Remarks: </b>{{$production->remark}}</p> --}}
                            </div>
                        </div>
                        <div class="card" v-if="products.length > 0">
                            <div class="card-header">
                                <h4 class="card-title">StockAdjust Items</h4>
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
                                        @forelse ($production->items as $row)
                                            <tr>
                                                <td>
                                                    <b>{{ $loop->iteration }}</b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->coi->name ?? ''}} </b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->coi->category ? $row->coi->category->name : '' }} </b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->coi->unit ? $row->coi->unit->name : '' }} </b>
                                                </td>

                                                <td>
                                                    <b>{{ $row->quantity }} </b>
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
            </section>
            <!-- Basic Inputs end -->
        </div>
    </div>
@endsection
