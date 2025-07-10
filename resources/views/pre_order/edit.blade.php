@extends('layouts.app')
@section('title')
    Pre Order Edit
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2" style="background: #343A40; padding:8px; border-radius:6px; color:white">
                <div class="col-sm-6">
                    <h1>Pre Order</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Pre Order</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Main content -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Pre Order Edit</h3>

                        </div>
                        <div class="card-body">
                            <form action="{{ route('pre-orders.update',encrypt($model->id)) }}" method="POST">
                                @csrf
                                @method('PUT')
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Order No </label>
                                        <input type="text" class="form-control" disabled
                                               value="{{ $model->order_number }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Outlet </label>
                                        <input type="text" class="form-control" disabled
                                               value="{{ $model->outlet->name }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Customer </label>
                                        <input type="text" class="form-control" disabled
                                               value="{{ $model->customer->name }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Delivery Date </label>
                                        <input type="date" class="form-control" name="delivery_date"
                                               value="{{ $model->delivery_date }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Delivery Time </label>
                                        <input type="time" class="form-control" name="delivery_time"
                                               value="{{ $model->delivery_time }}">
                                    </div>
                                </div>
                            </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Size </label>
                                            <input type="text" class="form-control" name="size"
                                                   value="{{ $model->size }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Flavour </label>
                                            <input type="text" class="form-control" name="flavour"
                                                   value="{{ $model->flavour }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Writing Note </label>
                                            <input type="text" class="form-control" name="cake_message"
                                                   value="{{ $model->cake_message }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Description </label>
                                            <textarea  class="form-control"
                                                      name="remark">{{ $model->remark }}</textarea>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mb-2">Update</button>
                                </div>
                            </form>

                            <div class="row">
                                <div class="col-sm-12 invoice-col">
                                    <div class="row">
                                        @if (isset($model->attachments))
                                            @foreach($model->attachments as $attachment)
                                                <div class="col-4">
                                                    <a target="_blank" href="{{ asset('/upload/'.$attachment->image) }}"
                                                       class="badge-light-info">
                                                        <img src="{{ asset('/upload/'.$attachment->image) }}"
                                                             class="rounded"
                                                             alt="" width="50%">
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <a target="_blank"
                                               href="{{ asset('admin/app-assets/dummy/dammy.jpg') }}">

                                    <span class="b-avatar-img">
                                        <img src="{{ asset('admin/app-assets/dummy/dammy.jpg') }}">
                                    </span>
                                            </a>
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <!-- /.row -->

                        <!-- this row will not appear when printing -->
                    </div>
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <!-- /.content-wrapper -->
@endsection
