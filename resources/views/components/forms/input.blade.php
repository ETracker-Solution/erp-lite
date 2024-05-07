<div>
    <input type="{{ $inputType }}" name="{{ $inputName }}" class="form-control" placeholder="{{$placeholder}}"
           @if($isRequired) required @endif value="{{ old($inputName, $defaultValue ?? '') }}"
           @if($isReadonly) disabled @endif>
    <x-forms.error inputName="{{ $inputName }}"/>
</div>
