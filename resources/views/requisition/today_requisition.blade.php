@extends('layouts.app')
@section('title')
    Today FG Requisitions
@endsection
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Today FG Requisitions' => '',
        ];
        $rowCount = is_countable($values ?? null) ? count($values) : 0;
        $outletCount = is_countable($outlets ?? null) ? count($outlets) : 0;
    @endphp
    <x-breadcrumb title='Today FG Requisitions' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info erp-today-req">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <h3 class="card-title mb-0">Outlet requisitions for today</h3>
                                <div class="erp-today-req__meta mt-1">
                                    Production need by product across outlets — scroll to see all columns.
                                </div>
                            </div>
                            <div class="card-tools mt-2 mt-md-0">
                                <x-buttons.pdf route="{{ route('today.requisitions.export', 'pdf') }}"/>
                                <x-buttons.excel route="{{ route('today.requisitions.export', 'xlsx') }}"/>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="erp-today-req__chips mb-3">
                                <span class="erp-today-req__chip">
                                    <strong>{{ $rowCount }}</strong> products needed
                                </span>
                                <span class="erp-today-req__chip">
                                    <strong>{{ $outletCount }}</strong> outlets
                                </span>
                                <span class="erp-today-req__chip is-soft">
                                    {{ now()->format('d M Y') }}
                                </span>
                            </div>

                            @if($rowCount === 0)
                                <div class="erp-today-req__empty">
                                    No outstanding FG requisitions for today.
                                </div>
                            @else
                                <div class="table_sticky erp-today-req__matrix">
                                    @include('exports.todays_requisition')
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .erp-today-req .card-header {
            gap: 12px;
        }

        .erp-today-req__meta {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .erp-today-req__chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .erp-today-req__chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: var(--erp-accent-soft, #e7f2ec);
            color: var(--erp-accent-deep, #245540);
            font-size: 12px;
            font-weight: 600;
            border: 1px solid var(--erp-line, #e6e0d8);
        }

        .erp-today-req__chip strong {
            font-size: 13px;
        }

        .erp-today-req__chip.is-soft {
            background: #fff;
            color: var(--erp-muted, #6b625b);
        }

        .erp-today-req__empty {
            padding: 28px 16px;
            text-align: center;
            color: var(--erp-muted, #6b625b);
            background: var(--erp-paper, #f6f3ee);
            border: 1px dashed var(--erp-line, #e6e0d8);
            border-radius: var(--erp-radius, 12px);
            font-weight: 600;
        }

        .erp-today-req__matrix {
            overflow: auto;
            width: 100%;
            max-height: min(70vh, 800px);
            border: 1px solid var(--erp-line, #e6e0d8);
            border-radius: 10px;
            background: #fff;
        }

        .erp-today-req__matrix table {
            table-layout: fixed;
            width: max-content;
            min-width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .erp-today-req__matrix th,
        .erp-today-req__matrix td {
            border: 1px solid var(--erp-line, #e6e0d8);
            width: 110px;
            padding: 8px 10px;
            font-size: 13px;
            background: #fff;
        }

        .erp-today-req__matrix th {
            background: var(--erp-accent-soft, #e7f2ec);
            color: var(--erp-ink, #1c1410);
            font-weight: 700;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .erp-today-req__matrix td:not(:first-child) {
            color: var(--erp-accent-deep, #245540);
            text-align: center;
            font-variant-numeric: tabular-nums;
        }

        .erp-today-req__matrix td:first-child,
        .erp-today-req__matrix th:first-child {
            position: sticky;
            left: 0;
            z-index: 3;
            background: #faf8f5;
            min-width: 140px;
            text-align: left;
            font-weight: 600;
        }

        .erp-today-req__matrix td:nth-child(2),
        .erp-today-req__matrix th:nth-child(2) {
            position: sticky;
            left: 140px;
            z-index: 3;
            background: #faf8f5;
            min-width: 160px;
            text-align: left;
        }

        .erp-today-req__matrix td:last-child,
        .erp-today-req__matrix th:last-child {
            position: sticky;
            right: 0;
            z-index: 3;
            background: #fff4e5;
            font-weight: 700;
            color: var(--erp-warn, #c47b1a);
        }

        .erp-today-req__matrix thead tr th:first-child,
        .erp-today-req__matrix thead tr th:nth-child(2),
        .erp-today-req__matrix thead tr th:last-child {
            z-index: 4;
            background: var(--erp-accent, #2f6b4f);
            color: #fff;
        }
    </style>
@endpush
