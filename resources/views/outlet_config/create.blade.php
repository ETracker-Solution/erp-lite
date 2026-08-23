@extends('layouts.app')
@section('title')
    Outlet Payment Config
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .erp-outlet-config__meta {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.82rem;
            font-weight: 500;
        }

        .erp-outlet-config__toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .erp-outlet-config__toolbar .form-control {
            max-width: 280px;
            border-radius: 10px;
        }

        .erp-outlet-config__check {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            color: #6b625b;
            margin: 0;
        }

        .erp-outlet-config__hint {
            margin-left: auto;
            color: #6b625b;
            font-size: 0.85rem;
        }

        .erp-outlet-config__table-wrap {
            max-height: min(70vh, 720px);
            overflow: auto;
            border: 1px solid #e6e0d8;
            border-radius: 12px;
        }

        .erp-outlet-config__table {
            margin-bottom: 0;
            min-width: 1200px;
        }

        .erp-outlet-config__table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #f6f3ee;
            white-space: nowrap;
        }

        .erp-outlet-config__sticky {
            position: sticky;
            left: 0;
            z-index: 3;
            background: #fff;
            min-width: 190px;
            box-shadow: 2px 0 0 rgba(0, 0, 0, 0.04);
        }

        .erp-outlet-config__table thead .erp-outlet-config__sticky {
            z-index: 4;
            background: #f6f3ee;
        }

        .erp-outlet-config__table .badge {
            margin-left: 6px;
            font-weight: 600;
        }

        .erp-outlet-config__table td {
            vertical-align: middle;
            min-width: 170px;
        }
    </style>
@endsection

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'System Admin Module' => '',
            'System Config' => '',
            'Outlet Payment' => '',
        ];
    @endphp
    <x-breadcrumb title='Outlet Payment Config' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('outlet-configs.store') }}" method="POST">
                @csrf
                <div class="card card-info erp-outlet-config">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h3 class="card-title mb-0">Map payment methods → COA</h3>
                            <div class="erp-outlet-config__meta mt-1">
                                Same method list as outlet create / POS.
                                Saving also links the ledger under Outlet Accounts.
                            </div>
                        </div>
                        <div class="card-tools mt-2 mt-md-0">
                            <a href="{{ route('outlet-accounts.index') }}" class="btn btn-sm btn-outline-light">
                                Outlet Accounts
                            </a>
                            <a href="{{ route('outlets.create') }}" class="btn btn-sm btn-primary">
                                New Outlet
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="erp-outlet-config__toolbar mb-3">
                            <input type="search" id="outletFilter" class="form-control"
                                   placeholder="Filter outlets…" autocomplete="off">
                            <label class="erp-outlet-config__check">
                                <input type="checkbox" id="missingOnly"> Show incomplete only
                            </label>
                            <span class="erp-outlet-config__hint">
                                {{ count($rows) }} outlets · {{ count($paymentTypes) }} methods
                            </span>
                        </div>

                        <div class="table-responsive erp-outlet-config__table-wrap">
                            <table class="table table-bordered table-sm erp-outlet-config__table" id="outletConfigTable">
                                <thead>
                                <tr>
                                    <th class="erp-outlet-config__sticky">Outlet</th>
                                    @foreach($paymentTypes as $type)
                                        <th>{{ $type }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($rows as $row)
                                    <tr class="outlet-config-row"
                                        data-name="{{ strtolower($row['name']) }}"
                                        data-missing="{{ $row['missing'] }}">
                                        <td class="erp-outlet-config__sticky">
                                            <strong>{{ $row['name'] }}</strong>
                                            @if($row['missing'] > 0)
                                                <span class="badge badge-warning">{{ $row['missing'] }} missing</span>
                                            @else
                                                <span class="badge badge-success">complete</span>
                                            @endif
                                        </td>
                                        @foreach($paymentTypes as $type)
                                            @php $selected = $row['values'][$type] ?? null; @endphp
                                            <td>
                                                <select name="settings[{{ $row['id'] }}][{{ $type }}]"
                                                        class="form-control form-control-sm select2-config">
                                                    <option value="">— Select —</option>
                                                    @foreach($bankCashLedgers as $account)
                                                        @php
                                                            $isLinked = in_array($account->id, $row['linked_coa_ids'], true);
                                                        @endphp
                                                        <option value="{{ $account->id }}"
                                                            {{ (int) $selected === (int) $account->id ? 'selected' : '' }}>
                                                            {{ $isLinked ? '★ ' : '' }}{{ $account->display_name }}
                                                        </option>
                                                    @endforeach
                                                    @if($selected && !$bankCashLedgers->contains('id', (int) $selected))
                                                        @php $extra = $ledgers->firstWhere('id', (int) $selected); @endphp
                                                        @if($extra)
                                                            <option value="{{ $extra->id }}" selected>
                                                                {{ $extra->display_name }}
                                                            </option>
                                                        @endif
                                                    @endif
                                                </select>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($paymentTypes) + 1 }}" class="text-center text-muted py-4">
                                            No outlets found. Create an outlet first.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">
                            ★ = already linked on Outlet Accounts for that outlet. Dropdown lists Bank/Cash ledgers only.
                        </small>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-info">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            Save configurations
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('js_scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            // Lazy Select2 — avoid initializing hundreds of widgets on load.
            $(document).on('focus', 'select.select2-config', function () {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    return;
                }
                $el.select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: '— Select —',
                    allowClear: true
                });
                $el.select2('open');
            });

            function filterRows() {
                const q = ($('#outletFilter').val() || '').toString().trim().toLowerCase();
                const missingOnly = $('#missingOnly').is(':checked');

                $('#outletConfigTable .outlet-config-row').each(function () {
                    const $row = $(this);
                    const name = ($row.data('name') || '').toString();
                    const missing = parseInt($row.data('missing'), 10) || 0;
                    $row.toggle((!q || name.indexOf(q) !== -1) && (!missingOnly || missing > 0));
                });
            }

            $('#outletFilter').on('input', filterRows);
            $('#missingOnly').on('change', filterRows);
        });
    </script>
@endpush
