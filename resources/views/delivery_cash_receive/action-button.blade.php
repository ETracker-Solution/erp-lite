<div class="project-actions text-right text-nowrap">
    <a href="{{ route('delivery-cash-receives.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    @if($row->status === 'pending')
        <a href="{{ route('delivery-cash-receives.show', encrypt($row->id)) }}" class="btn btn-xs btn-success" title="Receive">
            <i class="fas fa-check-circle"></i> Receive
        </a>
    @endif
</div>
