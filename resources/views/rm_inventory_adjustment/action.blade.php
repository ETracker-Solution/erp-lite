<div class="project-actions text-right">
    <form action="{{route('fg-inventory-adjustments.destroy', $row->id)}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf
        <a href="{{ route('fg-inventory-adjustments.edit', $row->id) }}" class="btn btn-info btn-xs">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a>
        <a href="{{ route('fg-inventory-adjustments.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a>
        <a target="_blank" href="{{ route('sale.pdf', $row->id) }}" class="btn btn-xs btn-info">
            <i class="fas fa-file-pdf"></i>
            PDF
        </a>
        <button id="btnDelete" class="btn btn-danger btn-xs"> <i class="fas fa-trash">
            </i> Delete</button>
    </form>
</div>
