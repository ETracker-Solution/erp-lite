<div>
    <div class="form-group">
        <x-forms.label label="{{ $label }}" isRequired="{{isset($isRequired) ? $isRequired : false}}"/>
        <select name="{{ $inputName }}" id="" @if(isset($isRequired) ? $isRequired : false) required @endif @if($isReadonly) readonly @endif class="select2 form-control">
            <option value="">{{ $placeholder }}</option>
            @foreach($options as $option)
                <option
                    value="{{ $option->$optionId }}" {{ $option->$optionId == old($inputName, $defaultValue) ? 'selected' : '' }}>{{ $option->$optionValue }}</option>
            @endforeach
        </select>
        <x-forms.error inputName="{{$inputName}}"/>
    </div>
</div>
