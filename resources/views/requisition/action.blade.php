<div class="project-actions text-right">
    <form action="{{route('requisitions.destroy', $row->id)}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf
        @if($row->status != 'completed')
            <a href="{{ route('requisitions.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
                <i class="fas fa-pencil-alt">
                </i>
                Edit
            </a>
        @endif
        <a href="{{ route('requisitions.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a>
        <button id="btnDelete" class="btn btn-danger btn-xs"> <i class="fas fa-trash">
            </i> Delete</button>
    </form>
</div>
