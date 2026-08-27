<div class="project-actions text-right text-nowrap">
    <a href="{{ route('memberships.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    <form action="{{ route('memberships.destroy', encrypt($row->id)) }}" method="post" class="d-inline"
          onsubmit="return confirm('If you delete this membership, it cannot be reverted.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-xs btn-danger" title="Delete">
            <i class="fas fa-trash"></i> Delete
        </button>
    </form>
</div>
