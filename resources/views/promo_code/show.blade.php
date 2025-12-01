@extends('layouts.app')
@section('title', 'Promo Code Details')
@section('content')
    @php
        $links = [
            'Home'=>route('dashboard'),
            'Loyalty Module'=>'',
            'Loyalty Entry'=>'',
            'Promo Code List'=>route('promo-codes.index'),
            'Promo Code Details'=>''
        ]
    @endphp
    <x-breadcrumb title='Promo Code Details' :links="$links"/>
    <section class="content">
        <div class="container-fluid">
            <!-- Promo Code Information -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Promo Code Information</h4>
                            <div class="card-tools">
                                <a href="{{route('promo-codes.edit', $promoCode->id)}}">
                                    <button class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                    </button>
                                </a>
                                <a href="{{route('promo-codes.index')}}">
                                    <button class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i> Back to List
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Code</th>
                                            <td><span class="badge badge-primary">{{ $promoCode->code }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Discount Type</th>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ ucfirst($promoCode->discount_type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Discount Value</th>
                                            <td>
                                                @if($promoCode->discount_type == 'percentage')
                                                    {{ $promoCode->discount_value }}%
                                                @else
                                                    {{ number_format($promoCode->discount_value, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Minimum Purchase</th>
                                            <td>{{ number_format($promoCode->minimum_purchase, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Discount Amount</th>
                                            <td>{{ number_format($promoCode->total_discount_amount ?? 0, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Start Date</th>
                                            <td>{{ \Carbon\Carbon::parse($promoCode->start_date)->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>End Date</th>
                                            <td>{{ \Carbon\Carbon::parse($promoCode->end_date)->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @php
                                                    $now = \Carbon\Carbon::now();
                                                    $start = \Carbon\Carbon::parse($promoCode->start_date);
                                                    $end = \Carbon\Carbon::parse($promoCode->end_date);
                                                @endphp
                                                @if($now->lt($start))
                                                    <span class="badge badge-secondary">Upcoming</span>
                                                @elseif($now->between($start, $end))
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Expired</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Discount For</th>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ str_replace('_', ' ', ucwords($promoCode->discount_for)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $promoCode->created_at->format('d M Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h4 class="card-title">Usage Statistics</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Uses</span>
                                            <span class="info-box-number">{{ $usageStats['total_uses'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fa fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Unique Customers</span>
                                            <span class="info-box-number">{{ $usageStats['unique_customers'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fa fa-money-bill"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Discount Given</span>
                                            <span class="info-box-number">{{ number_format($promoCode->used_discount_amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-primary">
                                        <span class="info-box-icon"><i class="fa fa-dollar-sign"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Balance After Discount</span>
                                            <span class="info-box-number">{{ number_format(($promoCode->total_discount_amount - $promoCode->used_discount_amount), 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Details -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 class="card-title">Usage Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="usageTable">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice No</th>
                                        <th>Customer Name</th>
                                        <th>Customer Phone</th>
                                        <th>Sale Date</th>
                                        <th>Subtotal</th>
                                        <th>Discount Amount</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($usageDetails as $index => $sale)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $sale->invoice_number ?? 'N/A' }}</strong></td>
                                            <td>{{ $sale->customer->name ?? 'Guest' }}</td>
                                            <td>{{ $sale->customer->mobile ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y h:i A') }}</td>
                                            <td>{{ number_format($sale->subtotal ?? 0, 2) }}</td>
                                            <td>
                                                    <span class="badge badge-success">
                                                        {{ number_format($sale->discount ?? 0, 2) }}
                                                    </span>
                                            </td>
                                            <td><strong>{{ number_format($sale->grand_total ?? 0, 2) }}</strong></td>
                                            <td>
                                                <a href="{{ route('sales.show', encrypt($sale->id)) }}" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <p class="text-muted">No usage found for this promo code yet.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('#usageTable').DataTable({
                "order": [[4, "desc"]], // Sort by date descending
                "pageLength": 25,
                "responsive": true,
                "language": {
                    "emptyTable": "No usage records found for this promo code"
                }
            });
        });
    </script>
@endpush
