<div class="project-actions text-right" style="display: ruby">
    @if($row->status == 'pending' && auth()->user()->employee->user_of != 'outlet')
        <form action="{{ route('requisitions.status-update', $row->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="approved">
            <button id="btnApprove" class="btn btn-success btn-xs"><i class="fas fa-check-circle">
                </i> Approve
            </button>
        </form>
    @endif
    @if($row->status == 'pending' && auth()->user()->employee->user_of != 'outlet')
        <form action="{{ route('requisitions.status-update', $row->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button id="btnDelete" class="btn btn-danger btn-xs"><i class="fas fa-check-circle">
                </i> Cancelled
            </button>
        </form>
    @endif
    <form action="{{route('requisitions.destroy', $row->id)}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf
        @if($row->status != 'approved' && $row->status != 'completed')
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
        @if($row->status != 'approved' && $row->status != 'completed')
            {{-- <button id="btnDelete" class="btn btn-danger btn-xs"><i class="fas fa-trash">
                </i> Delete
            </button> --}}
        @endif

    </form>
</div>
<script>
    confirmAlert('#btnApprove',message = "You won't be able to revert this!", buttonText = 'Yes, Approve it!', title = 'Are you sure?')
    confirmAlert('#btnDelete',message = "You won't be able to revert this!", buttonText = 'Yes, cancel it!', title = 'Are you sure?')
</script>
