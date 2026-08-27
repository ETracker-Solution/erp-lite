<div class="project-actions text-right text-nowrap">
    @if($row->status == 'pending' && auth()->user()->employee->user_of != 'outlet')
        <form action="{{ route('requisitions.status-update', $row->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="approved">
            <button id="btnApprove-{{ $row->id }}" class="btn btn-success btn-xs" type="submit">
                <i class="fas fa-check-circle"></i> Approve
            </button>
        </form>
        <form action="{{ route('requisitions.status-update', $row->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button id="btnCancel-{{ $row->id }}" class="btn btn-danger btn-xs" type="submit">
                <i class="fas fa-times-circle"></i> Cancel
            </button>
        </form>
    @endif
    @if($row->status == 'pending')
        <a href="{{ route('requisitions.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs" title="Edit">
            <i class="fas fa-pencil-alt"></i> Edit
        </a>
    @endif
    <a href="{{ route('requisitions.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    <a href="{{ route('requisition.pdf', encrypt($row->id)) }}" class="btn btn-xs btn-secondary" target="_blank"
       rel="noopener" title="PDF">
        <i class="fas fa-file-pdf"></i> PDF
    </a>
    @if($row->status === 'approved' && in_array($row->delivery_status, ['pending', 'partial'], true))
        <a href="{{ route('fg-requisition-deliveries.create', ['requisition_id' => $row->id]) }}"
           class="btn btn-xs btn-success" title="Deliver">
            <i class="fas fa-truck"></i> Deliver
        </a>
    @endif
</div>
<script>
    confirmAlert('#btnApprove-{{ $row->id }}', "You won't be able to revert this!", 'Yes, Approve it!', 'Are you sure?')
    confirmAlert('#btnCancel-{{ $row->id }}', "You won't be able to revert this!", 'Yes, cancel it!', 'Are you sure?')
</script>
