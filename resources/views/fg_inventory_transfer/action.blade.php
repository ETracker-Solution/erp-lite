<div class="project-actions text-right text-nowrap">
    <a href="{{ route('fg-inventory-transfers.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    <a href="{{ route('fg-inventory-transfers.pdf', encrypt($row->id)) }}" class="btn btn-xs btn-secondary" target="_blank"
       rel="noopener" title="PDF">
        <i class="fas fa-file-pdf"></i> PDF
    </a>
    @if($row->status === 'pending')
        <a href="{{ route('fg-transfer-receives.create', ['transfer_id' => $row->id]) }}" class="btn btn-xs btn-success" title="Receive">
            <i class="fas fa-download"></i> Receive
        </a>
    @endif
</div>
