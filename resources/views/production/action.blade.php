<div class="project-actions text-right">

    <form action="{{ route('productions.destroy', encrypt($row->id)) }}" method="POST">
        @csrf
        @method('DELETE')
        <a href="{{ route('productions.show',$row->id)}}" title="View" class="btn btn-xs btn-primary">
            <i class="fas fa-eye ml-1"></i>
        </a>
        {{-- <a href="{{ route('productions.edit',encrypt($row->id))}}" title="Edit"  class="btn btn-info btn-xs">
            <i class="fas fa-edit ml-1"></i>
        </a> --}}
        {{-- <a href="javascript:void(0)" id="delete_confirm"  class="btn btn-danger btn-xs"> <i class="fas fa-trash buttonColor ml-1"></i></a> --}}
    </form>
</div>

