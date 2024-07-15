<div class="project-actions text-right" style="display: ruby">
    @if($row->status == 'pending' && auth()->user()->employee->user_of != 'outlet')
        <form action="{{ route('pre-orders.status-update', $row->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="approved">
            <button id="btnDelete" class="btn btn-success btn-xs"> <i class="fas fa-check-circle">
                </i> Approve</button>
        </form>
    @endif
    <form action="{{route('pre-orders.destroy', encrypt($row->id))}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf
        {{-- <a href="{{ route('pre-orders.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a> --}}
        <a href="{{ route('pre-orders.show', $row->id) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a>
        @if($row->status == 'pending')
        <button id="btnDelete" class="btn btn-danger btn-xs"><i class="fas fa-trash">
            </i> Delete
        </button>
        @endif
    </form>
</div>
