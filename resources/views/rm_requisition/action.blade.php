<div class="project-actions text-right">
    <form action="{{route('rm-requisitions.destroy', $row->id)}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf

        <a href="{{ route('rm-requisitions.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a>
        @if($row->status != 'completed')
            <a href="{{ route('rm-requisitions.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
                <i class="fas fa-pencil-alt">
                </i>
                Edit
            </a>
{{--            <button id="btnDelete" class="btn btn-danger btn-xs"> <i class="fas fa-trash">--}}
{{--                </i> Delete</button>--}}
        @endif

    </form>
</div>
<script>
    confirmAlert('#btnDelete')
</script>
