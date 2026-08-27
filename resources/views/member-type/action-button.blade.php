<div class="project-actions text-right text-nowrap">
    <a href="{{ route('member-types.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    <a href="{{ route('member-types.edit', encrypt($row->id)) }}" class="btn btn-xs btn-info" title="Edit">
        <i class="fas fa-pencil-alt"></i> Edit
    </a>
</div>
