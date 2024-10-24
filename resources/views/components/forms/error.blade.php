<div>
    @if ($errors->has($inputName))
        <small
            class="text-danger">{{ $errors->first($inputName) }}</small>
    @endif
</div>
