<div class="d-flex text-left">
    <div class="d-flex flex-column">
        @foreach($data as $key=>$row)
            <h6 class="mb-0"><b><span>{{ $key }} :</span></b> {!! $row !!}</h6>
        @endforeach
    </div>
</div>
