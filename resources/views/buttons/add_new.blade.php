@if(isset($data_target))
<button type="button" class="btn btn-primary ml-1" data-toggle="modal" data-target="{{ $data_target }}"><i data-feather='plus-square'></i>{{__('Add New')}}</button>     
@elseif(isset($route))
    <a href="{{ $route }}" class="btn btn-primary">
        <i data-feather='plus-square'></i>
        {{__('Add New')}}</a>
    @else
    pass route
@endif
