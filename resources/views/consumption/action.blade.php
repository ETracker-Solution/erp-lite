{{--<div class="text-center btnAlign">--}}
{{--    --}}{{-- <a href="{{ route('factory.productions.edit',Illuminate\Support\Facades\Crypt::encrypt($row->id))}}" title="Edit">--}}
{{--        <i class="fas fa-edit"></i>--}}
{{--    </a> --}}
{{--    <a href="{{ route('consumptions.show',encrypt($row->id))}}" title="View">--}}
{{--        <i class="fas fa-eye ml-1"></i>--}}
{{--    </a>--}}
{{--</div>--}}
<div class="project-actions text-right">
    <form action="{{route('consumptions.destroy', encrypt($row->id))}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf
        <a href="{{ route('consumptions.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a>
        <a href="{{ route('consumptions.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a>
        <button id="btnDelete" class="btn btn-danger btn-xs"> <i class="fas fa-trash">
            </i> Delete</button>
    </form>
</div>
