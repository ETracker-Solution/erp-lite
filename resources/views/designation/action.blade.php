<div class="project-actions text-center">

    <form action="{{route('designations.destroy', encrypt($row->id))}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf

        <a href="{{ route('designations.edit',encrypt($row->id))}}" class="btn btn-info btn-xs">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a>

        {{-- <a href="{{ route('designations.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a> --}}
{{--        <button id="btnDelete" class="btn btn-danger btn-xs"> <i class="fas fa-trash">--}}
{{--            </i> Delete</button>--}}
    </form>
</div>
<script>
    confirmAlert('#btnDelete')
</script>
