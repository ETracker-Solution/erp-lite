@php
    $actions = [
                    'show'=>route('purchase-returns.show', $row->id),
                ];
@endphp

<div class="d-flex justify-content-center">
    <x-action-component :actions="$actions" status="{{ $row->status }}" />
</div>



