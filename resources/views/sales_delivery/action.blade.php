<div class="project-actions text-right text-nowrap">
    <a href="{{ route('sales-deliveries.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    @if($row->status === 'pending')
        <a href="{{ route('sales-deliveries.create', ['sale_id' => $row->id]) }}"
           class="btn btn-xs btn-success" title="Deliver">
            <i class="fas fa-truck"></i> Deliver
        </a>
    @endif
</div>
