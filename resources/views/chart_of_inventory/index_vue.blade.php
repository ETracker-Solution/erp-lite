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
            <div class="row" id="vue_app">
                <div class="col-lg-4 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" style="color:#115548;">Chart Of Inventory</h3>
                            <div class="card-tools">

                            </div>
                        </div>
                        <div class="card-body">

                            <div id="app">
                                <div v-for="folder in folders">
                                    <span @click="toggle(folder.name)" style="cursor: pointer;">+ @{{ folder.name }}</span>
                                    <div v-show="false" :id="folder.name">
                                        <div v-for="page in folder.pages" style="margin-left: 20px;">
                                            <span> - @{{ page.name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" style="color:#115548;">Chart Of Inventory Create</h3>
                            <div class="card-tools">

                            </div>
                        </div>
                        <div class="card-body">


                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
        <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('css')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endsection
@push('script')

    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function () {

            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {  },
                    folders: [
                        { name: 'Search Engine', pages: [{name: 'Google'}, {name: 'Yahoo!'}, {name: 'goo!'}] },
                        { name: 'SNS', pages: [{name: 'Facebook'}, {name: 'Twitter'}, {name: 'Google+'}, {name: 'mixi'}] },
                        { name: 'Shpping', pages: [{name: 'Amazon'}, {name: 'ebay'}] }
                    ]
                },
                computed: {},
                methods: {
                    toggle: function (idname) {
                        if( document.getElementById(idname).style.display == "none" ){
                            document.getElementById(idname).style.display = "inline";
                        }else{
                            document.getElementById(idname).style.display = "none";
                        }
                    },
                },

                updated() {
                    $('.bSelect').selectpicker('refresh');
                }

            });

            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });

        });
    </script>
@endpush
