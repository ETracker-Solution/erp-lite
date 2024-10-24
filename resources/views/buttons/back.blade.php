@if(isset($route))
    <a href="{{ $route }}" class="btn btn-primary" style="margin: 1px">
        <i class="fa fa-reply"></i>
        {{__('Back')}}</a>
    @else
    pass route
@endif
