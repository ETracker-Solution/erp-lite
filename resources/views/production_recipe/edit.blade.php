@extends('layouts.app')
@section('title', 'Recipe')

@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Pre-define Recipe Edit'=>''
        ]
    @endphp
    <x-breadcrumb title='Pre-define Recipe' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('production-recipes.update',$recipes[0]->uid) }}" method="POST" class="">
                        @csrf
                        @method('put')
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Pre-define Recipe Edit</h3>
                                <div class="card-tools">
                                    <a href="{{route('production-recipes.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars"
                                           aria-hidden="true"></i> &nbsp;
                                           See List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="card-box">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="uid">Recipe No</label>
                                                <input type="text" class="form-control input-sm"
                                                       name="uid" id="uid"
                                                        readonly value="{{ $recipes[0]->uid }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="fg_group_id">FG Group</label>
                                                <input type="text" class="form-control input-sm"
                                                       name="fg_group" id="fg_group"
                                                        readonly value="{{ $recipes[0]->item->parent->name }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label for="fg_item_id">FG Item</label>
                                                <input type="text" class="form-control input-sm"
                                                       name="fg_group" id="fg_group"
                                                        readonly value="{{ $recipes[0]->item->name }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Pre-define Recipe Item</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" style="width: 100%">
                                                <thead class="bg-secondary">
                                                <tr>
                                                    <th style="width: 5%">#</th>
                                                    <th style="width: 20%">Group</th>
                                                    <th style="width: 20%">Item</th>
                                                    <th style="width: 20%">Unit</th>
                                                    <th style="width: 20%">Qty</th>
                                                    <th style="width: 15%">Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recipes as $recipe)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $recipe->coi->parent->name }}</td>
                                                            <td>{{ $recipe->coi->name }}</td>
                                                            <td>{{ $recipe->coi->unit->name }}</td>
                                                            <td>
                                                                <input type="number" step="0.00001" name="recipes[{{ $recipe->id }}][qty]" class="form-control input-sm"
                                                                    value="{{ $recipe->qty }}" required>
                                                            </td>
                                                            <td>
                                                                <select name="recipes[{{ $recipe->id }}][status]" class="form-control input-sm">
                                                                    <option value="active" {{ $recipe->status == 'active' ? 'selected' : '' }}>Active</option>
                                                                    <option value="inactive" {{ $recipe->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="float-right btn btn-primary" type="submit"><i
                                        class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div> <!-- end col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('css')

@endsection
@push('style')
    <style>
        .categoryLoader {
            position: absolute;
            top: 50%;
            right: 40%;
            transform: translate(-50%, -50%);
            color: red;
            z-index: 999;
        }

        input[placeholder="Select date"] {
            display: block;
            width: 100%;
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            box-shadow: inset 0 0 0 transparent;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush
@section('js')

@endsection
@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="https://cms.diu.ac/vue/vuejs-datepicker.min.js"></script>

@endpush
