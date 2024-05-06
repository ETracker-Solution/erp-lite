@extends('layouts.app')
@section('title')
Sale
@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-1">

        </div>
    </div><!-- /.container-fluid -->
</section>



<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Chart Of Inventory</h3>
                        <div class="card-tools">

                        </div>
                    </div>
                    <div class="card-body">
                        <link rel="stylesheet"
                            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                        <div class="menu">
                            <ul class="tree">
                                @if (isset($allChartOfInventories))
                                @foreach ($allChartOfInventories as $row)
                                <li>
                                    <span class="branch" onclick="changeChart()" id="{{ $row->id }}"><i
                                            class="fa fa-folder-o"></i>
                                        {{ $row->name }}
                                    </span>

                                    @if (count($row->subChartOfInventories))
                                    @include('chart_of_inventory.sub-group-list', [
                                    'subcharts' => $row->subChartOfInventories,
                                    ])
                                    @endif

                                </li>
                                @endforeach
                                @endif
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-8">
                <form action="{{ route('chart-of-inventories.store') }}" method="POST" class="" enctype="multipart/form-data">
                    @csrf
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Chart of Inventory Create</h4>
                        </div>
                        <hr style="margin: 0;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-12">
                                    <x-forms.select label="Inventory Parent Name" inputName="parent_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="$groups" optionId='id' optionValue='name'/>
                                </div>

                                <div class="col-xl-12 col-md-12 col-12">
                                    <x-forms.static-select label="Group/Item" inputName="type" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="['group','item']"/>
                                </div>
                                <div class="col-xl-12 col-md-12 col-12">
                                    <x-forms.text label="Group Name" inputName="name" placeholder="Enter Name" :isRequired='true'  :isReadonly='false' defaultValue=""/>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="float-right btn btn-primary waves-effect waves-float waves-light"
                                type="submit">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- end col -->
    </div>
    <!-- /.row -->

    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
@section('css')
<style>
    body {
        background: #fff;
        font-family: arial;
        color: #333;
    }

    .menu {
        width: 300px;
        margin: auto;
        margin-bottom: 50px;
    }

    .tree {
        list-style: none;
        padding-left: 20px;
        position: relative;
        color: #333;
    }

    .tree:before {
        content: "";
        width: 2px;
        background: #a6d2ff;
        top: 0;
        bottom: 3px;
        left: 0;
        position: absolute;
    }

    .tree li {
        position: relative;
    }

    .tree li:before {
        content: "";
        width: 20px;
        height: 1px;
        position: absolute;
        font-family: "FontAwesome";
        top: 12px;
        left: -20px;
        position: absolute;
    }

    .tree li:hover,
    .tree li:focus {
        color: #333;
        cursor: pointer;
    }

    .tree .tree {
        display: none;
    }

    .fa {
        padding-right: 5px;
        margin-top: 10px;
    }
</style>
@endsection
@push('script')
<script>
    $('.branch').click(function() {
        $(this).children().toggleClass('fa-folder-open-o');
        $(this).next().slideToggle();

    });
    var click = 0
    function changeChart(parent_id){
        $('select[name="parent_id"]').val($(event.target)[0].id).change().attr('selected', 'selected')
        event.preventDefault
    }

</script>
@endpush