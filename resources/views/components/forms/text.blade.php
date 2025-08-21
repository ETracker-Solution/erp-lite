<div>
    <div class="form-group">
        <x-forms.label label="{{ $label }}" isRequired="{{isset($isRequired) ? $isRequired : false}}"/>
        <x-forms.input label="{{ $label }}" inputType="text" inputName="{{ $inputName }}" placeholder="{{ $placeholder }}" isRequired="{{isset($isRequired) ? $isRequired : false}}"  isReadonly="{{$isReadonly}}" defaultValue="{{$defaultValue}}"/>
    </div>
</div>
