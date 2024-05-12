@extends('layouts.app')
@section('title')
    Permission Create
@endsection
@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection
@section('content')
@php
$links = [
'Home'=>route('dashboard'),
'Permission Create'=>''
]
@endphp
<x-breadcrumb title='Permission' :links="$links"/>



    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{route('permissions.store')}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <!-- Horizontal Form -->
                        <div class="row">
                            <div class="col-xl-3 col-md-3 col-4 mb-1">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-9 col-8 mb-1">
                                <div class="form-group">
                                    <label for="tags">Permissions</label>
                                    <select class="form-control select2" multiple="multiple" name="permissions[]" id="tags">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary waves-effect">Reset</button>
                            </div>
                        </div>
                        <!-- /.card -->
                    </form>
                </div>
                <div class="col-2"></div>
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@push('js')
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

        })
    </script>
    <script>
        $(document).ready(function() {
            $('#tags').select2({
                placeholder: {
                    id: ' ', // the value of the option
                    text: 'Separate Permissions by a comma'
                },
                tags: true,
                tokenSeparators: [',',' '],
                allowClear: true,
            });
        });
    </script>
    

@endpush
