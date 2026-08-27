@extends('layouts.app')

@section('title', 'Sales Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Sales' => route('sales.index'),
            'Details' => '',
        ];
        $subtotalFmt = number_format((float) $sale->subtotal, 2);
        $grandFmt = number_format((float) $sale->grand_total, 2);
        $discountFmt = number_format((float) $sale->discount, 2);
        $paidAmount = number_format((float) ($sale->receive_amount ?? 0), 2);
        $dueAmount = number_format(max((float) $sale->grand_total - (float) ($sale->receive_amount ?? 0), 0), 2);
    @endphp
    <x-breadcrumb title="Sales" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Invoice {{ $sale->invoice_number ?: ('#'.$sale->id) }}
                                <span class="ml-2">{!! showStatus($sale->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('sale.pdf-download', encrypt($sale->id)) }}"
                                   class="btn btn-sm btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $sale->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Outlet</div>
                                    <div class="font-weight-bold">{{ $sale->outlet->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Customer</div>
                                    <div class="font-weight-bold">{{ $sale->customer->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Mobile</div>
                                    <div class="font-weight-bold">{{ $sale->customer->mobile ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:5%">#</th>
                                        <th>Item</th>
                                        <th class="text-right">Rate</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($sale->items as $item)
                                        @php
                                            $line = ((float) ($item->unit_price ?? 0) * (float) ($item->quantity ?? 0))
                                                - (float) ($item->discount ?? 0);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->name ?? '—' }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->unit_price ?? 0), 2) }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->quantity ?? 0), 2) }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->discount ?? 0), 2) }}</td>
                                            <td class="text-right">{{ number_format($line, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No items</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6 offset-md-6">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <th>Subtotal</th>
                                            <td class="text-right">{{ $subtotalFmt }}</td>
                                        </tr>
                                        <tr>
                                            <th>Delivery Charge</th>
                                            <td class="text-right">{{ number_format((float) $sale->delivery_charge, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Additional Charge</th>
                                            <td class="text-right">{{ number_format((float) $sale->additional_charge, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Discount</th>
                                            <td class="text-right">{{ $discountFmt }}</td>
                                        </tr>
                                        @if($sale->couponCode)
                                            <tr>
                                                <th>Coupon ({{ $sale->couponCode }})</th>
                                                <td class="text-right">{{ number_format((float) $sale->couponCodeDiscountAmount, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th class="text-primary">Grand Total</th>
                                            <td class="text-right font-weight-bold text-primary">{{ $grandFmt }}</td>
                                        </tr>
                                        <tr>
                                            <th>Paid</th>
                                            <td class="text-right">{{ $paidAmount }}</td>
                                        </tr>
                                        <tr>
                                            <th>Due</th>
                                            <td class="text-right">{{ $dueAmount }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->is_super || (auth()->user()->employee && auth()->user()->employee->user_of == 'ho'))
                        <div class="text-center mb-3">
                            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" id="salesDelete" type="submit">Delete</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
@push('js_scripts')
    <script>
        $(document).ready(() => {
            confirmAlert('#salesDelete')
        })
    </script>
@endpush
