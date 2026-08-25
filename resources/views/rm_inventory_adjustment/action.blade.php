<div class="project-actions text-right text-nowrap">
    <a href="{{ route('rm-inventory-adjustments.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    <form action="{{ route('rm-inventory-adjustments.destroy', $row->id) }}" method="post" class="d-inline">
        @csrf
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" id="btnDelete" class="btn btn-xs btn-danger" title="Delete">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
<script>
    confirmAlert('#btnDelete');
</script>
