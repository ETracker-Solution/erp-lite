<div class="project-actions text-right">
    <form action="{{route('fund-transfer-vouchers.destroy', encrypt($row->id))}}" method="post">
        <input type="hidden" name="_method" value="DELETE">
        @csrf
        @if($row->status == 'pending' && auth()->user()->employee->user_of != 'outlet')
            <a href="{{ route('fund-transfer-vouchers.receive', encrypt($row->id)) }}" class="btn btn-xs btn-secondary">
                <i class="fas fa-thumps-up">
                </i> Receive
            </a>
        @endif
        @if($row->status == 'pending')
            <a href="{{ route('fund-transfer-vouchers.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
                <i class="fas fa-pencil-alt">
                </i>
                Edit
            </a>
        @endif
        <a href="{{ route('fund-transfer-vouchers.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary">
            <i class="fas fa-folder">
            </i> Show
        </a>
{{--        <button id="btnDelete" class="btn btn-danger btn-xs"> <i class="fas fa-trash">--}}
{{--            </i> Delete</button>--}}
    </form>
</div>
<script>
    confirmAlert('#btnDelete')
</script>
