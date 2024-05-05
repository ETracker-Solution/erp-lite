@extends('layouts.app')
@section('title')
    Category
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Category Create'=>''
            ]
        @endphp
        <x-bread-crumb-component title='Category' :links="$links"/>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Horizontal Form -->
                    <form action="{{route('categories.store')}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Category Create</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-6 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   placeholder="Enter Name" value="{{old('name')}}">
                                            @if($errors->has('name'))
                                                <small class="text-danger">{{$errors->first('name')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label>Parent</label>
                                            <select class="select2 form-control" name="parent_id">
                                                <option value="" selected disabled>Select One</option>
                                                @forelse($categories as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('parent_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}
                                                    </option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="description"><b>Description</b></label>
                                            <textarea id="description" name="description"
                                                      placeholder="Enter Description" class="form-control input-sm"
                                                      rows="2">{{old('description')}}</textarea>
                                            @if($errors->has('description'))
                                                <small class="text-danger">{{$errors->first('description')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary waves-effect waves-float waves-light float-right"
                                        type="submit">Submit
                                </button>
                            </div>
                        </div>
                </div>
                </form>
                <!-- /.card -->
            </div>
            <div class="col-2"></div>
        </div>
        <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@push('script-bottom')


@endpush
