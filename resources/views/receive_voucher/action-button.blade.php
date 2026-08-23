<div class="project-actions text-right">
    <a href="{{ route('receive-vouchers.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
        <i class="fas fa-folder"></i> Show
    </a>
    <a href="{{ route('receive-vouchers.edit', encrypt($row->id)) }}" class="btn btn-xs btn-info">
        <i class="fas fa-pencil-alt"></i> Edit
    </a>
    <a href="{{ route('receive-voucher.pdf', encrypt($row->id)) }}" class="btn btn-xs btn-secondary" target="_blank" rel="noopener">
        <i class="fas fa-download"></i> PDF
    </a>
</div>
