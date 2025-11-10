<div class="btnAlign" style="
    display: flex;
    align-items: center;
">
    <a href="{{ route('promo-codes.show',encrypt($row->id))}}" title="Edit">
        <i class="fas fa-eye ml-1"></i>
    </a>
    @if($row->sms_count < 1)
    <a href="{{ route('promo-codes.edit',encrypt($row->id))}}" title="Edit">
        <i class="fas fa-edit ml-1"></i>
    </a>
    @endif
    <form action="{{ route('promo-codes.send-sms', encrypt($row->id)) }}" method="POST">
        @csrf
        <a class="text-success" id="txt" href="javascript:void(0)"> <i class="fas fa-envelope ml-1"></i> </a>
    </form>
{{--    <form action="{{ route('promo-codes.destroy', encrypt($row->id)) }}" method="POST">--}}
{{--        @csrf--}}
{{--        @method('DELETE')--}}
{{--        <a href="javascript:void(0)" id="delete_confirm"> <i class="fas fa-trash buttonColor ml-1"></i></a>--}}
{{--    </form>--}}
</div>
<script>
    confirmAlert('#txt', "If you confirm, it cannot be reverted", "Yes, Send SMS")
    // confirmAlert('#delete_confirm', "If you delete this Item, it cannot be reverted")
</script>




