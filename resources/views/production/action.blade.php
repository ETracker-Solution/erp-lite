<div class="text-center btnAlign">
    @if($row->status=="pending")
        <a href="{{ route('factory.productions.stock',Illuminate\Support\Facades\Crypt::encrypt($row->id))}}" title="Stock">
            <i class="fas fa-database ml-1"></i>
        </a>
    @endif
    <a href="{{ route('factory.productions.show',$row->id)}}" title="View">
        <i class="fas fa-eye ml-1"></i>
    </a>
</div>
