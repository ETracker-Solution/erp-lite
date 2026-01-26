@extends('layouts.app')
@section('title')
    FGInventory Adjustment Details
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Inventory Adjustment'=>''
        ]
    @endphp
    <x-breadcrumb title='FG Inventory Adjustment Details' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Main content -->
                    <div class="invoice p-3 mb-3">
                        <!-- info row -->
                        <!-- info row -->
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                <table width="100%">
                                    <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>Adjusted By :</b> {{ $fGInventoryAdjustment->createdBy->name }}</p>
                                            <p><b>FGID No :</b> {{ $fGInventoryAdjustment->uid }}</p>
                                            <p><b>Date :</b> {{ $fGInventoryAdjustment->date }} </p>
                                            <p><b>Store :</b> {{ $fGInventoryAdjustment->store->name }} </p>
                                            <p><b>Transaction Type
                                                    :</b> {!! showStatus($fGInventoryAdjustment->transaction_type) !!}
                                            </p>
                                            <p><b>Status :</b> {!! showStatus($fGInventoryAdjustment->status) !!}</p>
                                            <p><b>Remarks :</b> {{ $fGInventoryAdjustment->remark ??'N/A' }} </p>
                                            <p><b>Reference :</b> {{ $fGInventoryAdjustment->reference_no ?? 'N/A' }}
                                            </p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">

                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <!-- Table row -->
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Group</th>
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '' }}</td>
                                            <td>{{ $item->coi->name ?? '' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '' }}</td>
                                            <td>{{ $item->rate ?? '' }}</td>
                                            <td>{{ $item->quantity ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>

                        @if($fGInventoryAdjustment->editHistories->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Edit Histories</h5>
                                <div class="timeline">
                                    @foreach($fGInventoryAdjustment->editHistories->sortByDesc('created_at') as $history)
                                    <div>
                                        <i class="fas fa-edit bg-blue"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> {{ $history->created_at->format('d M Y h:i A') }}</span>
                                            <h3 class="timeline-header">Edited by <a href="#">{{ $history->user->name ?? 'Unknown' }}</a></h3>
                                            <div class="timeline-body">
                                                <strong>Reason:</strong> {{ $history->remarks }}
                                                <div class="mt-2">
                                                    @php
                                                        $old = json_decode($history->old_data, true);
                                                        $new = json_decode($history->new_data, true);
                                                        $old_items = collect($old['items'] ?? []);
                                                        $new_items = collect($new['items'] ?? []);
                                                        $all_coi_ids = $old_items->pluck('coi_id')->merge($new_items->pluck('coi_id'))->unique();
                                                    @endphp
                                                    <table class="table table-sm table-bordered mt-2">
                                                        <thead>
                                                            <tr class="bg-light">
                                                                <th>Item Name</th>
                                                                <th class="text-center">Prev Qty</th>
                                                                <th class="text-center">Current Qty</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($all_coi_ids as $coi_id)
                                                                @php
                                                                    $old_item = $old_items->where('coi_id', $coi_id)->first();
                                                                    $new_item = $new_items->where('coi_id', $coi_id)->first();
                                                                    $item_name = $new_item['coi']['name'] ?? $old_item['coi']['name'] ?? null;

                                                                    if (!$item_name) {
                                                                        $coi = \App\Models\ChartOfInventory::find($coi_id);
                                                                        $item_name = $coi ? $coi->name : 'Unknown Item';
                                                                    }
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $item_name }}</td>
                                                                    <td class="text-center">{{ $old_item['quantity'] ?? 0 }}</td>
                                                                    <td class="text-center">{{ $new_item['quantity'] ?? 0 }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    <div>
                                        <i class="fas fa-history bg-gray"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if($fGInventoryAdjustment->status == 'adjusted' &&  auth()->user()->is_super)
                        <form action="{{ route('fg-inventory-adjustments.update',$fGInventoryAdjustment->id) }}"
                              method="post">
                            @csrf
                            @method('PUT')
                            <button id="cancelAdjustment" class="btn btn-sm btn-danger">Cancel</button>
                        </form>
                    @endif
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- /.content -->

    <!-- /.content-wrapper -->
@endsection
@push('js_scripts')
    <script>
        $(document).ready(() => {
            confirmAlert('#cancelAdjustment')
        })
    </script>
@endpush
