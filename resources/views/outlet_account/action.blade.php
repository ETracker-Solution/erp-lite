<div class="project-actions text-right">
    <a href="{{ route('outlet-accounts.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
        <i class="fas fa-pencil-alt"></i> Edit
    </a>
    <form action="{{ route('outlet-account.change', $row->id) }}" method="POST" class="d-inline" title="Toggle status">
        @csrf
        @method('PATCH')
        @if ($row->status == 'inactive')
            <input type="hidden" name="status" value="active">
            <button class="btn btn-success btn-xs" type="submit">Active</button>
        @else
            <input type="hidden" name="status" value="inactive">
            <button class="btn btn-warning btn-xs" type="submit">Inactive</button>
        @endif
    </form>
    <form action="{{ route('outlet-accounts.destroy', encrypt($row->id)) }}" method="POST" class="d-inline"
          onsubmit="return confirm('Remove this outlet account link?');">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-xs" type="submit"><i class="fas fa-trash"></i></button>
    </form>
</div>
