<div class="btnAlign">
    <a href="{{ route('promo-codes.show',encrypt($row->id))}}" title="Edit">
        <i class="fas fa-eye ml-1"></i>
    </a>
    <a href="{{ route('promo-codes.edit',encrypt($row->id))}}" title="Edit">
        <i class="fas fa-edit ml-1"></i>
    </a>
{{--    <form action="{{ route('promo-codes.destroy', encrypt($row->id)) }}" method="POST">--}}
{{--        @csrf--}}
{{--        @method('DELETE')--}}
{{--        <a href="javascript:void(0)" id="delete_confirm"> <i class="fas fa-trash buttonColor ml-1"></i></a>--}}
{{--    </form>--}}
</div>
<script>
    confirmAlert('#delete_confirm', "If you delete this Item, it cannot be reverted")
</script>




