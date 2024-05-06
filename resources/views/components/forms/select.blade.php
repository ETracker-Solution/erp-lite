<div>
    <div class="form-group">
        <x-forms.label label="{{ $label }}" isRequired="{{$isRequired}}"/>
        <select name="{{ $inputName }}" id="" @if($isRequired) required @endif @if($isReadonly) readonly @endif>
            <option value="">{{ $placeholder }}</option>
            @foreach($options as $option)
                <option
                    value="{{ $option->id }}" {{ $option->id == old($inputName, $defaultValue) ? 'selected' : '' }}>{{ $option->value }}</option>
            @endforeach
        </select>
        <x-forms.error inputName="{{$inputName}}"/>
    </div>
</div>
