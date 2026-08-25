@php
    $factoryId = auth()->user()?->employee?->factory_id;
    $canReceive = $row->status === 'pending' && (
        !$factoryId
        || (
            $row->toStore
            && $row->toStore->doc_type === 'factory'
            && (int) $row->toStore->doc_id === (int) $factoryId
        )
    );
@endphp
<div class="project-actions text-right text-nowrap">
    <a href="{{ route('rm-inventory-transfers.show', $row->id) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    <a href="{{ route('rm-inventory-transfers.pdf', $row->id) }}" class="btn btn-xs btn-secondary" target="_blank" rel="noopener" title="PDF">
        <i class="fas fa-file-pdf"></i> PDF
    </a>
    @if($canReceive)
        <a href="{{ route('rm-transfer-receives.create', ['transfer_id' => $row->id]) }}"
           class="btn btn-xs btn-success" title="Receive">
            <i class="fas fa-check"></i> Receive
        </a>
    @endif
    @if($row->status === 'pending')
        <form action="{{ route('rm-inventory-transfers.destroy', $row->id) }}" method="post" class="d-inline">
            @csrf
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" id="btnDelete" class="btn btn-xs btn-danger" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endif
</div>
<script>
    confirmAlert('#btnDelete');
</script>
