<div class="project-actions text-right text-nowrap">
    <a href="{{ route('purchases.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    <a href="{{ route('purchase.pdf-download', encrypt($row->id)) }}" class="btn btn-xs btn-secondary" target="_blank"
       rel="noopener" title="PDF">
        <i class="fas fa-file-pdf"></i> PDF
    </a>
    @if($row->status !== 'returned')
        <a href="{{ route('purchase-returns.create', ['purchase_id' => $row->id]) }}" class="btn btn-xs btn-warning" title="Return">
            <i class="fas fa-undo"></i> Return
        </a>
    @endif
</div>
