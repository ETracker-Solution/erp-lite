@extends('pos.layouts.app')
@section('content')
    <div id="changeableDiv">
        @include('pos.pos-page')
        @include('pos.customer-page')
        @include('pos.order-page')
{{--        @include('pos.pre-order-page')--}}
    </div>
@endsection
