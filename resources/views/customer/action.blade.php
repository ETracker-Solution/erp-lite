<div class="project-actions text-right">
    <a href="{{ route('customers.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
        <i class="fas fa-pencil-alt">
        </i>
        Edit
    </a>
    <a href="{{ route('customers.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
        <i class="fas fa-folder">
        </i> Show
    </a>
</div>
