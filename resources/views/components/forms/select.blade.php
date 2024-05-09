<div>
    <div class="form-group">
        <x-forms.label label="{{ $label }}" isRequired="{{$isRequired}}"/>
        <select name="{{ $inputName }}" id="" @if($isRequired) required @endif @if($isReadonly) readonly @endif class="select2 form-control">
            <option value="">{{ $placeholder }}</option>
            @foreach($options as $option)
                <option
                    value="{{ $option->$optionId }}" {{ $option->$optionId == old($inputName, $defaultValue) ? 'selected' : '' }}>{{ $option->$optionValue }}</option>
            @endforeach
        </select>
        <x-forms.error inputName="{{$inputName}}"/>
    </div>
</div>
