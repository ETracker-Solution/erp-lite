<div>
    <div class="form-group">
        <x-forms.label label="{{ $label }}" isRequired="{{isset($isRequired) ? $isRequired : false}}"/>
        <select name="{{ $inputName }}" id="" @if(isset($isRequired) ? $isRequired : false) required @endif @if($isReadonly) disabled @endif class="form-control">
            <option value="" selected>{{ $placeholder }}</option>
            @foreach($options as $option)
                <option value="{{ $option }}" {{ $option == old($inputName, $defaultValue) ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        <x-forms.error inputName="{{$inputName}}"/>
    </div>
</div>
