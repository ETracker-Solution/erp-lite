<div class="project-actions text-right text-nowrap">
    <a href="{{ route('rm-requisitions.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    @if($row->status === 'pending')
        <a href="{{ route('rm-requisition-deliveries.create', ['requisition_id' => $row->id]) }}"
           class="btn btn-xs btn-success" title="Deliver">
            <i class="fas fa-truck"></i> Deliver
        </a>
        <a href="{{ route('rm-requisitions.edit', encrypt($row->id)) }}" class="btn btn-xs btn-info" title="Edit">
            <i class="fas fa-pencil-alt"></i> Edit
        </a>
    @endif
</div>
