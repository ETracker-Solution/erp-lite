<div class="text-center btnAlign">
    {{-- <a href="{{ route('factory.productions.edit',Illuminate\Support\Facades\Crypt::encrypt($row->id))}}" title="Edit">
        <i class="fas fa-edit"></i>
    </a> --}}
    <a href="{{ route('factory.consumptions.show',$row->id)}}" title="View">
        <i class="fas fa-eye ml-1"></i>
    </a>
</div>
