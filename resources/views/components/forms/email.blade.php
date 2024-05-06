<div>
    <div class="form-group">
        <x-forms.label label="{{ $label }}" isRequired="{{$isRequired}}"/>
        <x-forms.input label="{{ $label }}" inputType="email" inputName="{{ $inputName }}"
                       placeholder="{{ $placeholder }}" isRequired="{{$isRequired}}" isReadonly="{{$isReadonly}}"
                       defaultValue="{{$defaultValue??''}}"/>
    </div>
</div>
