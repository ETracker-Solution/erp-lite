@extends('admin.layouts.app')
@section('title', 'Purchase Return')
@section('content')
    <div class="content-wrapper">
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Purchase Return'=>route('admin.purchase-returns.index'),
            'Purchase Return Details'=>''
            ]
        @endphp
        <x-bread-crumb-component title='Purchase Return Details' :links="$links"/>
        <div class="content-body">
            <!-- Basic Inputs start -->
            <section id="basic-input">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Purchase Return Details</h4>
                                <div class="text-right">
                                    <a href="{{ route('admin.purchase.return.pdf',encrypt($purchaseReturn->id)) }}" class="btn btn-primary" target="_blank">
                                        <i class="fa fa-download"></i> PDF</a>
                                    @include('buttons.back', [
                                        'route' => route('admin.purchase-returns.index'),
                                    ])
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left; padding:8px;" width="34%">
                                                <p class="lead marginbottom payment-info"><b>Purchase Return Details</b></p>
                                                <p><b>Purchase No :</b> {{ $purchaseReturn->purchase_return_number }} </p>
                                                <p><b>Date :</b> {{ $purchaseReturn->created_at->format('Y-m-d') }} </p>
                                                <p><b>Status :</b> {!! showStatus($purchaseReturn->status) !!} </p>
                                            </td>
                                            <td style="text-align: left; padding:8px;" width="33%">
                                                <p class="lead marginbottom payment-info"><b>Purchase Return Details</b></p>
                                                <p><b>Sub Total :</b> {{ $purchaseReturn->subtotal }} </p>
                                                <p><b>Grand Total :</b> {{ $purchaseReturn->grand_total }} </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="mt-1"><b>Remarks: </b>{{$purchaseReturn->remark}}</p>
                            </div>
                        </div>
                        <div class="card" v-if="products.length > 0">
                            <div class="card-header">
                                <h4 class="card-title">Purchase Items</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr style="background-color: #80808021;">
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Unit</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Item Total</th>
                                        </tr>
                                        @forelse ($purchaseReturn->items as $row)
                                            <tr>
                                                <td>
                                                    <b>1</b>
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
                                                    <b>{{ $row->unit_price }} </b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->quantity }} </b>
                                                </td>
                                                <td>
                                                    <b>{{ $row->unit_price * $row->quantity }} </b>
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

                                            </td>
                                            <td>Sub Total</td>
                                            <td class="text-right">{{$purchaseReturn->subtotal}} </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Grand Total
                                            </td>
                                            <td class="text-right">{{$purchaseReturn->grand_total}} </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            {{-- <div class="card-footer">
                                <div class="float-right">
                                    <a href="{{route('admin.purchases.index')}}" class="btn btn-primary"><i
                                            class="fa fa-fw fa-lg fa-reply"></i>Back</a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </section>
            <!-- Basic Inputs end -->
        </div>
    </div>
@endsection
