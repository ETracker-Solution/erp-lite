@extends('layouts.app')
@section('title','Dashboard')
@push('style')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('admin/app-assets/vendors/css/charts/apexcharts.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('admin/app-assets/css/pages/dashboard-ecommerce.css') }}">
    <style>
        .dash-alert-pill {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            margin-left: 6px;
        }
        .dash-alert-pill.danger { background: #fde8e8; color: #c0392b; }
        .dash-alert-pill.warn { background: #fff3cd; color: #856404; }
        .dash-change-up { color: #28a745; font-weight: 700; }
        .dash-change-down { color: #dc3545; font-weight: 700; }
        .dash-meta { font-size: 12px; color: #6c757d; }
        .card-header.bg-info .dash-meta {
            color: rgba(255, 255, 255, 0.9);
        }
        .dash-quick-link {
            display: block;
            padding: 10px 14px;
            border: 1px solid var(--erp-line, #e3e6ea);
            border-radius: 8px;
            color: inherit;
            text-decoration: none;
            height: 100%;
        }
        .dash-quick-link:hover { text-decoration: none; color: inherit; background: #f8f9fa; }
        .dash-quick-link i { margin-right: 6px; color: #3d9970; }
        .dash-outlet-mini {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 12px;
        }
        .dash-outlet-mini-row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px;
            opacity: 0.95;
        }
        .dash-share-bar {
            height: 6px;
            border-radius: 999px;
            background: #e9ecef;
            overflow: hidden;
            margin-top: 4px;
        }
        .dash-share-bar > span {
            display: block;
            height: 100%;
            background: #3d9970;
            border-radius: 999px;
        }
        .dash-outlet-zero { color: #adb5bd; }
    </style>
@endpush
@section('content')
    <section id="dashboard-ecommerce">
        <x-breadcrumb title="Dashboard" :links="[]"/>
    </section>
    <section class="content">
        <div class="container-fluid">
            {{-- Primary KPIs --}}
            <div class="row">
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="erp-kpi accent">
                        <div class="label">Today's Sales</div>
                        <div class="value">{{ number_format($totalSales, 2) }}</div>
                        <div class="hint">
                            {{ $todayInvoice }} invoice{{ $todayInvoice === 1 ? '' : 's' }}
                            · {{ $outletsWithSalesToday ?? 0 }} outlet{{ ($outletsWithSalesToday ?? 0) === 1 ? '' : 's' }} selling
                        </div>
                        @if(($todaySalesByOutlet ?? collect())->where('total', '>', 0)->isNotEmpty())
                            <div class="dash-outlet-mini">
                                @foreach(($todaySalesByOutlet ?? collect())->where('total', '>', 0)->take(3) as $row)
                                    <div class="dash-outlet-mini-row">
                                        <span>{{ \Illuminate\Support\Str::limit($row['name'], 18) }}</span>
                                        <span>{{ number_format($row['total'], 0) }} ({{ $row['share'] }}%)</span>
                                    </div>
                                @endforeach
                                @if(($outletsWithSalesToday ?? 0) > 3)
                                    <div class="dash-outlet-mini-row" style="opacity: .8;">
                                        <span>+{{ $outletsWithSalesToday - 3 }} more below</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <div class="erp-kpi">
                        <div class="label">Month to Date</div>
                        <div class="value">{{ number_format($monthSales, 0) }}</div>
                        <div class="hint">
                            @if($monthChangePct >= 0)
                                <span class="dash-change-up">▲ {{ $monthChangePct }}%</span>
                            @else
                                <span class="dash-change-down">▼ {{ abs($monthChangePct) }}%</span>
                            @endif
                            vs last month ({{ number_format($lastMonthSales, 0) }})
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <a href="{{ route('pre-orders.index') }}" class="erp-kpi-link">
                        <div class="erp-kpi">
                            <div class="label">Pre-Orders Due Today</div>
                            <div class="value">
                                {{ $preOrderDueToday }}
                                @if($preOrderOverdue > 0)
                                    <span class="dash-alert-pill danger">{{ $preOrderOverdue }} overdue</span>
                                @endif
                            </div>
                            <div class="hint">{{ $preOrderDueThisWeek }} due this week · {{ $preOrderPending }} pending total</div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6 col-12 mb-3">
                    <a href="{{ route('requisitions.index') }}" class="erp-kpi-link">
                        <div class="erp-kpi">
                            <div class="label">Pending FG Requisitions</div>
                            <div class="value">
                                {{ $pendingRequisitions }}
                                @if($pendingRequisitions > 0)
                                    <span class="dash-alert-pill warn">needs action</span>
                                @endif
                            </div>
                            <div class="hint">Awaiting approval or delivery</div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Secondary stats --}}
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card card-statistics">
                        <div class="card-header bg-info d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Today's Snapshot</h4>
                            <span class="dash-meta">Aggregates refresh every 60s · {{ $cacheRefreshedAt ?? '' }}</span>
                        </div>
                        <div class="card-body statistics-body">
                            <div class="row">
                                <div class="col-xl-2 col-sm-4 col-6 mb-2">
                                    <x-card-statistics title="Outlets" value="{{ $outlets }}" icon="store"
                                                       colorClass="bg-light-primary"/>
                                </div>
                                <div class="col-xl-2 col-sm-4 col-6 mb-2">
                                    <x-card-statistics title="Customers" value="{{ $customers }}" icon="users"
                                                       colorClass="bg-light-info"/>
                                </div>
                                <div class="col-xl-2 col-sm-4 col-6 mb-2">
                                    <x-card-statistics title="FG Products" value="{{ $products }}" icon="shopping-cart"
                                                       colorClass="bg-light-danger"/>
                                </div>
                                <div class="col-xl-2 col-sm-4 col-6 mb-2">
                                    <x-card-statistics title="Invoices" value="{{ $todayInvoice }}" icon="file-invoice"
                                                       colorClass="bg-light-primary"/>
                                </div>
                                <div class="col-xl-2 col-sm-4 col-6 mb-2">
                                    <x-card-statistics title="Discount" value="{{ number_format($todayDiscount, 0) }}"
                                                       icon="percent" colorClass="bg-light-warning"/>
                                </div>
                                <div class="col-xl-2 col-sm-4 col-6 mb-2">
                                    <x-card-statistics title="Wastage" value="{{ number_format($wastageAmount) }}"
                                                       icon="trash-alt" colorClass="bg-light-success"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="row">
                <div class="col-lg-7 col-12 mb-3">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h4 class="card-title mb-0">Sales — Last 7 Days</h4>
                        </div>
                        <div class="card-body">
                            <div id="sales-trend-chart" style="min-height: 280px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-info d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Today's Sales by Outlet</h4>
                            <span class="dash-meta">
                                {{ $outletsWithSalesToday ?? 0 }} active
                                @if(($outletsWithNoSalesToday ?? 0) > 0)
                                    · {{ $outletsWithNoSalesToday }} no sales yet
                                @endif
                            </span>
                        </div>
                        <div class="table-responsive" style="max-height: 320px;">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Outlet</th>
                                    <th class="text-right">Sales</th>
                                    <th class="text-center">Inv.</th>
                                    <th style="width: 28%;">Share</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($todaySalesByOutlet ?? [] as $row)
                                    <tr class="{{ $row['total'] <= 0 ? 'dash-outlet-zero' : '' }}">
                                        <td>{{ $row['name'] }}</td>
                                        <td class="text-right font-weight-bold">
                                            {{ number_format($row['total'], 0) }}
                                        </td>
                                        <td class="text-center">{{ $row['invoices'] }}</td>
                                        <td>
                                            <div class="small mb-1">{{ $row['share'] }}%</div>
                                            <div class="dash-share-bar">
                                                <span style="width: {{ min($row['share'], 100) }}%;"></span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No outlet sales recorded today</td>
                                    </tr>
                                @endforelse
                                </tbody>
                                @if($totalSales > 0)
                                    <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td>Total</td>
                                        <td class="text-right">{{ number_format($totalSales, 0) }}</td>
                                        <td class="text-center">{{ $todayInvoice }}</td>
                                        <td>100%</td>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action tables --}}
            <div class="row">
                <div class="col-lg-4 col-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-info d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Due Today</h4>
                            <a href="{{ route('pre-orders.index') }}" class="btn btn-xs btn-light btn-sm">View all</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($preOrdersDueToday as $row)
                                    <tr>
                                        <td>
                                            <a href="{{ route('pre-orders.show', $row->id) }}">
                                                {{ $row->order_number ?: '#'.$row->id }}
                                            </a>
                                            <div class="small text-muted">
                                                {{ $row->deliveryPoint->name ?? '—' }}
                                                @if($row->delivery_time)
                                                    · {{ \Carbon\Carbon::parse($row->delivery_time)->format('h:i A') }}
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $row->customer->name ?? '—' }}</td>
                                        <td class="text-right">{{ number_format((float) $row->grand_total, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No pre-orders due today</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-info d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Overdue Pre-Orders</h4>
                            @if($preOrderOverdue > 5)
                                <span class="badge badge-danger">+{{ $preOrderOverdue - 5 }} more</span>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Due</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($overduePreOrders as $row)
                                    <tr>
                                        <td>
                                            <a href="{{ route('pre-orders.show', $row->id) }}">
                                                {{ $row->order_number ?: '#'.$row->id }}
                                            </a>
                                            <div class="small text-muted">{{ $row->deliveryPoint->name ?? '—' }}</div>
                                        </td>
                                        <td class="text-danger">{{ $row->delivery_date }}</td>
                                        <td class="text-right">{{ number_format((float) $row->grand_total, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No overdue pre-orders</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-info d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Pending Requisitions</h4>
                            <a href="{{ route('requisitions.index') }}" class="btn btn-xs btn-light btn-sm">View all</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Req #</th>
                                    <th>Outlet</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($pendingRequisitionRows as $row)
                                    <tr>
                                        <td>
                                            <a href="{{ route('requisitions.show', encrypt($row->id)) }}">
                                                {{ $row->uid }}
                                            </a>
                                            <div class="small text-muted">{{ $row->date }}</div>
                                        </td>
                                        <td>{{ $row->fromStore->name ?? '—' }}</td>
                                        <td>{!! showStatus($row->status) !!}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No pending requisitions</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick links --}}
            <div class="row mb-3">
                <div class="col-md-3 col-6 mb-2">
                    <a href="{{ route('sales.index') }}" class="dash-quick-link"><i class="fas fa-receipt"></i> Sales</a>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <a href="{{ route('pre-orders.index') }}" class="dash-quick-link"><i class="fas fa-calendar-check"></i> Pre-Orders</a>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <a href="{{ route('requisitions.index') }}" class="dash-quick-link"><i class="fas fa-truck-loading"></i> Requisitions</a>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <a href="{{ route('pos.get-view') }}" class="dash-quick-link"><i class="fas fa-cash-register"></i> POS</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script src="{{ asset('admin/app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var trendLabels = @json($salesTrend['labels'] ?? []);
            var trendValues = @json($salesTrend['values'] ?? []);

            if (document.querySelector('#sales-trend-chart') && typeof ApexCharts !== 'undefined') {
                new ApexCharts(document.querySelector('#sales-trend-chart'), {
                    chart: { type: 'area', height: 280, toolbar: { show: false }, zoom: { enabled: false } },
                    series: [{ name: 'Sales (BDT)', data: trendValues }],
                    xaxis: { categories: trendLabels },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 0.4, opacityFrom: 0.5, opacityTo: 0.05 } },
                    colors: ['#3d9970'],
                    dataLabels: { enabled: false },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                return val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val;
                            }
                        }
                    },
                    tooltip: {
                        y: { formatter: function (val) { return Number(val).toLocaleString(undefined, { maximumFractionDigits: 0 }); } }
                    }
                }).render();
            }
        });
    </script>
@endpush
