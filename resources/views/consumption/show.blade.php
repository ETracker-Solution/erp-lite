@extends('factory.layouts.app')
@section('title', 'Consumption')
@section('content')
    <div class="content-wrapper">
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Consumption'=>route('factory.stock-adjusts.index'),
            'Consumption Details'=>''
            ]
        @endphp
        <x-bread-crumb-component title='Consumption Details' :links="$links"/>
        <div class="content-body">
            <!-- Basic Inputs start -->
            <section id="basic-input">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Consumption Details</h4>
                                <div class="text-right">
                                    <a href="{{ route('factory.consumptions.pdf',encrypt($consumption->id)) }}" class="btn btn-primary" target="_blank">
                                        <i class="fa fa-download"></i> PDF</a>
                                    @include('buttons.back', [
                                        'route' => route('factory.stock-adjusts.index'),
                                    ])
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                    <tr>
                                        <th><strong>Consumption No</strong></th>
                                        <td>{{ $consumption->consumption_no }}</td>
                                        <th><strong>Date</strong></th>
                                        <td>{{ $consumption->created_at->format('Y-m-d') }}</td>
                                        <th><strong>Status </strong></th>
                                        <td>{!! showStatus($consumption->status) !!}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                {{-- <p class="mt-1"><b>Remarks: </b>{{$consumption->remark}}</p> --}}
                            </div>
                        </div>
                        <div class="card" v-if="products.length > 0">
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
                                                    <b>{{ $row->product->name }} </b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->product->category->name }} </b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->product->unit->name }} </b>
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
            </section>
            <!-- Basic Inputs end -->
        </div>
    </div>
@endsection
