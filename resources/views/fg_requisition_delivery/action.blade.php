<div class="project-actions text-right">
    <form action="{{route('fg-requisition-deliveries.destroy', $row->id)}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf
        {{-- <a href="{{ route('fg-requisition-deliveries.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a> --}}
        <a href="{{ route('fg-requisition-deliveries.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a>
        @if($row->status != 'received')
            <button id="btnDelete" class="btn btn-danger btn-xs"> <i class="fas fa-trash">
            </i> Delete</button>
        @endif
    </form>
</div>
<script>
    confirmAlert('#btnDelete')
</script>
