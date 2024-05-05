@extends('layouts.app')
@section('title')
Purchase
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
      <div class="col-lg-12 col-md-12">

        <div class="card">
          <div class="card-header">
            <h3 class="card-title" style="color:#115548;">New Purchase</h3>
            <div class="card-tools">
              <a href="{{route('purchases.index')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; Purchase List</button></a>
            </div>
          </div>
            <form action="{{ route('purchases.store') }}" method="POST" class="">
                @csrf
          <div class="card-body">

            <div class="card-box">
              <hr>
              <div id="">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                      <label for="supplier_id">Supplier</label>
                      <select name="supplier_id" id="supplier_id" class="form-control bSelect" v-model="supplier_id">
                        <option value="0">Walking Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                      <label for="category_id" class="control-label">Category</label>
                      <select class="form-control" name="category_id" v-model="category_id" @change="fetch_product">
                        <option value="">Select One</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <br>
                  <br>
                  <br>
                  <br>

                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" v-if="items.length>0">

                    <hr>
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Action</th>
                            <th>Item</th>
                            <th>Stock</th>
                            <th>Hawa</th>
                            <th>Qty</th>
                            <th>Buying Price</th>
                            <th>Selling Price</th>
                            <th>Subtotal</th>

                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(row, index) in items">
                            <td>
                              <button type="button" class="btn btn-danger" @click="delete_row(row)"><i class="fa fa-trash"></i></button>
                            </td>
                            <td>
                              <input type="hidden" :name="'products['+index+'][product_id]'" class="form-control input-sm" v-bind:value="row.id">
                              <input type="text" class="form-control input-sm" v-bind:value="row.name" readonly>
                            </td>
                            <td>
                              <input type="text" class="form-control input-sm" v-bind:value="row.stock" readonly>
                            </td>
                            <td>
                              <input type="text" class="form-control input-sm" v-bind:value="row.pre_order" readonly>
                            </td>
                            <td>
                              <input type="number" v-model="row.quantity" :name="'products['+index+'][quantity]'" class="form-control input-sm" @change="itemtotal(row)">
                            </td>
                            <td>
                              <input type="number" v-model="row.price" :name="'products['+index+'][buying_price]'" class="form-control input-sm" @change="itemtotal(row)">
                            </td>
                            <td>
                              <input type="number" v-model="row.selling_price" :name="'products['+index+'][selling_price]'" class="form-control input-sm">
                            </td>
                            <td>
                              <input type="text" class="form-control input-sm" v-bind:value="itemtotal(row)" readonly>
                            </td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td colspan="6">

                            </td>
                            <td>
                              Total
                            </td>
                            <td>
                              <input type="text" class="form-control input-sm" name="subtotal" v-bind:value="subtotal" readonly>
                            </td>
                          </tr>
                          <tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" v-if="items.length > 0">
                      <div class="text-right col-lg-12 col-md-12 col-sm-12 col-xs-12">
                          <textarea class="form-control" name="description" rows="5" placeholder="Enter Comments"></textarea>
                      </div>
                  </div>
                </div>



              </div>
            </div>

          </div>
                <div class="card-footer" v-if="items.length > 0">
                    <button class="float-right btn btn-primary" type="submit"><i
                            class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>
                </div>
            </form></div>


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
    <link rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush
@section('js')

@endsection
@push('script')
<script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
<script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
<script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>

<script>
  $(document).ready(function() {

    var vue = new Vue({
      el: '#vue_app',
      data: {
        config: {

          get_product_info_by_category_id_url: "{{ url('fetch-product-by-category-id') }}",
          // get_product_info_url: "{{ url('admin/fetch-product-info') }}",
        },
        supplier_id: 0,
        category_id: '',
        items: [],
        quantity: 0,
        buying_price: 0,
        price: 0,
        selling_price: 0,

      },
      computed: {

        subtotal: function() {
          return this.items.reduce((total, item) => {
            return total + item.quantity * item.price
          }, 0)
        }
      },
      methods: {

        fetch_product() {

          var vm = this;

          var slug = vm.category_id;

          if (slug) {
            axios.get(this.config.get_product_info_by_category_id_url + '/' + slug).then(function(response) {

              vm.items = response.data.products;
              vm.category_id = '';
            }).catch(function(error) {

              toastr.error('Something went to wrong', {
                closeButton: true,
                progressBar: true,
              });

              return false;

            });
          }

        },


        delete_row: function(row) {
          this.items.splice(this.items.indexOf(row), 1);
        },
        itemtotal: function(index) {

          console.log(index.quantity * index.price);
          return index.quantity * index.price;


          //   alert(quantity);
          //  var total= row.quantity);
          //  row.itemtotal=total;
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
