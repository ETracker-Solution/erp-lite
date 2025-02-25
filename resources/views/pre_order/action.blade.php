<div class="project-actions text-right" style="display: ruby">
    @if($row->status == 'pending' && auth()->user()->employee->user_of != 'outlet')
        @can('sales-pre-orders-approval')
{{--        <form action="{{ route('pre-orders.status-update', $row->id) }}" method="POST">--}}
{{--            @csrf--}}
{{--            @method('PUT')--}}
{{--            <input type="hidden" name="status" value="approved">--}}
{{--            <button id="btnDelete" class="btn btn-success btn-xs"><i class="fas fa-check-circle">--}}
{{--                </i> Approve--}}
{{--            </button>--}}
{{--        </form>--}}
            <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#ApprovalModal"
                    data-id="{{ $row->id }}"
                    type="button"><i
                    class="fas fa-check-circle">
                </i> Approve
            </button>
        @endcan
    @endif
    @if($row->status == 'pending' && (in_array(auth()->user()->employee->user_of,['ho']) || auth()->user()->is_super))
        <form action="{{ route('pre-orders.status-update', $row->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button class="btn btn-danger btn-xs"><i class="fas fa-check-circle">
                </i> Cancel
            </button>
        </form>
    @endif
    @if($row->status == 'pending')
        <a href="{{ route('pre-orders.edit', encrypt($row->id)) }}" class="btn btn-info btn-xs">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a>
    @endif
    <a href="{{ route('pre-orders.show', $row->id) }}" class="btn btn-xs btn-primary">
        <i class="fas fa-folder">
        </i> Show
    </a>
    @if($row->status == 'approved' && auth()->user()->employee->user_of != 'outlet')
{{--        <button class="btn btn-dark btn-xs" data-toggle="modal" data-target="#productionModal"--}}
{{--                data-id="{{ $row->id }}"--}}
{{--                type="button"><i--}}
{{--                class="fas fa-arrow-alt-circle-up">--}}
{{--            </i> Production--}}
{{--        </button>--}}
            <form action="{{ route('pre-orders.status-update', $row->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="ready_to_delivery">
                <button class="btn btn-dark btn-xs"><i class="fas fa-arrow-alt-circle-up">
                    </i> Production
                </button>
            </form>
    @endif
    @if($row->status == 'ready_to_delivery' && auth()->user()->employee->user_of != 'outlet')
        <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#deliverModal" data-id="{{ $row->id }}"
                type="button"><i
                class="fas fa-check-circle">
            </i> Deliver
        </button>
    @endif
    @if($row->status == 'delivered' && auth()->user()->employee->user_of != 'factory')
        <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#receiveModal" data-id="{{ $row->id }}"
                type="button"><i
                class="fas fa-check-circle">
            </i> Receive
        </button>
    @endif
</div>
