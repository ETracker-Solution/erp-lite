<div class="project-actions text-right">
    @if(auth()->user()->is_super)
        <a href="{{ route('rm-inventory-adjustments.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a>
    @endcan
    <a href="{{ route('rm-inventory-adjustments.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
        <i class="fas fa-folder">
        </i> Show
    </a>
</div>
