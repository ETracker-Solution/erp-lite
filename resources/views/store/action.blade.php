<div class="project-actions text-right">
    <form action="{{route('stores.destroy', encrypt($row->id))}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf
        {{-- <a href="#" class="btn btn-info btn-xs" onclick="getEditAbleData({{ $row }}, {{ route('stores.update',$row->id)}})">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a> --}}
        <a href="{{ route('stores.edit',encrypt($row->id))}}" class="btn btn-info btn-xs">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a>
        {{-- <a href="{{ route('stores.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a> --}}
        <button id="btnDelete" class="btn btn-danger btn-xs"> <i class="fas fa-trash">
            </i> Delete</button>
    </form>
</div>
