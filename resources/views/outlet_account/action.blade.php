<div class="project-actions text-right">
    <form action="{{route('outlet-accounts.destroy', encrypt($row->id))}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf
        {{-- <a href="{{ route('outlet-accounts.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a> --}}
        {{-- <a href="{{ route('supplier-groups.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a> --}}

        
        <form action="{{ route('outlet-account.change', $row->id) }}" method="POST" title="Accept">
            @csrf
            @method('PATCH')
            @if ($row->status == 'inactive')
                <input type="hidden" name="status" value="active">
                <button class="btn btn-success btn-sm"  id="activeStatus">
                    Active
                </button>
            @elseif($row->status == 'active')
                <input type="hidden" name="status" value="inactive">
                <button class="btn btn-danger btn-sm"  id="inactiveStatus">
                    Inactive
                </button>
            @endif
        </form>
        {{-- <button id="btnDelete" class="btn btn-danger btn-xs"> <i class="fas fa-trash">
            </i> Delete</button> --}}
    </form>
</div>
<script>
    confirmAlert('#activeStatus', message = "You won't be able to revert this!", buttonText = 'Yes, Change it!', title = 'Are you sure?')
    confirmAlert('#inactiveStatus' , message = "You won't be able to revert this!", buttonText = 'Yes, Change it!', title = 'Are you sure?')
</script>
