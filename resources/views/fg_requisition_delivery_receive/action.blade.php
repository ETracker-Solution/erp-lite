<div class="project-actions text-right text-nowrap">
    <a href="{{ route('fg-delivery-receives.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    <a href="{{ route('fg-delivery-receive.pdf', encrypt($row->id)) }}" class="btn btn-xs btn-secondary"
       target="_blank" rel="noopener" title="PDF">
        <i class="fas fa-file-pdf"></i> PDF
    </a>
</div>
