<div>
    <div class="form-group">
        <x-forms.label label="{{ $label }}" isRequired="{{$isRequired}}"/>
        <select name="{{ $inputName }}" id="" @if($isRequired) required @endif @if($isReadonly) readonly @endif class="form-control">
            <option value="" selected>{{ $placeholder }}</option>
            @foreach($options as $option)
                <option value="{{ $option }}" {{ $option == old($inputName, $defaultValue) ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        <x-forms.error inputName="{{$inputName}}"/>
    </div>
</div>
