<div class="project-actions text-right text-nowrap">
    <a href="{{ route('fg-requisition-deliveries.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    <a href="{{ route('fg-requisition-delivery.pdf', encrypt($row->id)) }}" class="btn btn-xs btn-secondary"
       target="_blank" rel="noopener" title="PDF">
        <i class="fas fa-file-pdf"></i> PDF
    </a>
    @if($row->status === 'completed')
        <a href="{{ route('fg-delivery-receives.create', ['requisition_delivery_id' => $row->id]) }}"
           class="btn btn-xs btn-success" title="Receive">
            <i class="fas fa-truck-loading"></i> Receive
        </a>
    @endif
    @if($row->status != 'received')
        <a href="{{ route('fg-requisition-deliveries.edit', encrypt($row->id)) }}" class="btn btn-xs btn-info" title="Edit">
            <i class="fas fa-pencil-alt"></i> Edit
        </a>
    @endif
</div>
