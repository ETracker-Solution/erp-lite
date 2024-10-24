@extends('admin.layouts.app')
@section('content')
    <section class="content-header">
        @php
        $links = [
        'Admin Dashboard'=>route('admin.admin_dashboard'),
        ]
        @endphp
        <x-breadcrumb title='Admin Dashboard' :links="$links" />
    </section>
@endsection