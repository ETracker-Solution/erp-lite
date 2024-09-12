@extends('layouts.app')
@section('title')
    Outlet Account
@endsection
@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Master Data'=>'',
            'Account Setting'=>'',
            'Outlet Account' . (isset($outletAccount) ? ' Edit' : ' Entry') => '',
        ];
    @endphp
    <x-breadcrumb title='Outlet Account' :links="$links" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form
                        @if (isset($outletAccount)) action="{{ route('outlet-accounts.update', $outletAccount->id) }}" @else action="{{ route('outlet-accounts.store') }}" @endif
                        method="POST" class="" enctype="multipart/form-data">
                        @csrf
                        @if (isset($outletAccount))
                            @method('PUT')
                        @endif
                        <!-- Horizontal Form -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Outlet Account</h3>
                                <div class="card-tools">
                                    <a href="{{ route('outlet-accounts.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List

                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-3 col-md-3 col-12 mb-1">
                                        <x-forms.select label="Oultet" inputName="outlet_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="$outlets" optionId="id" optionValue="name"/>
                                    </div>

                                    <div class="col-xl-3 col-md-3 col-12 mb-1">
                                        <x-forms.text label="Account Name" inputName="name" placeholder="Enter Account Name"
                                            :isRequired='true' :isReadonly='false' :defaultValue="isset($outletAccount) ? $outletAccount->name : ''" />
                                    </div>
                                    <div class="col-xl-3 col-md-3 col-12 mb-1">
                                        <x-forms.static-select label="Status" inputName="status" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="isset($outletAccount) ? $outletAccount->status : ''" :options="['active','inactive']"/>
                                    </div>
                                    <div class="col-xl-3 col-md-3 col-12 mb-1">
                                        <x-forms.static-select label="Type" inputName="type" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="isset($outletAccount) ? $outletAccount->status : ''" :options="['Cash','Bkash','Nagad','Rocket','Upay','DBBL','UCB']"/>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-info float-right"><i class="fa fa-check" aria-hidden="true"></i>
                                    Submit
                                </button>
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
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
@endpush
