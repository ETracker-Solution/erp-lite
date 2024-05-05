@extends('layouts.app')
@section('title') POS
@endsection
@section('css')
<!-- Select2 -->
<link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css">
<style>
.product-title{
    padding: 0px 2px;
    margin: 0;
    line-height: 16px;
    text-align: center;
}
</style>
@endsection
@section('css-bottom')
@endsection

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-1"></div>
	</div>
	<!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-5 col-md-6 col-xs-12">
				<div class="card">
					<div class="card-body">
						<form>
							<div class="form-group">
								<div class="input-group">
									<select class="custom-select select2">
										<option selected disabled>Select Customer</option>
										<option value="1">One</option>
										<option value="2">Two</option>
										<option value="3">Three</option>
									</select>
									<div class="input-group-append">
										<button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#add_customer_in_pos"><i class="fa fa-plus-circle" aria-hidden="true"></i>
										</button>
									</div>
								</div>
							</div>
							<div class="form-group">
								<select class="custom-select select2">
									<option selected disabled>Enter Product Name</option>
									<option value="1">One</option>
									<option value="2">Two</option>
									<option value="3">Three</option>
								</select>
							</div>
							<table class="table table-sm tabel-responsive">
								<thead>
									<tr class="table-success">
										<th scope="col" class="text-left">Product</th>
										<th scope="col" class="text-center">Rate</th>
										<th scope="col" class="text-center">Qty</th>
										<th scope="col" class="text-right">Price</th>
										<th scope="col" class="text-right"><i class="fa fa-trash" aria-hidden="true"></i>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-left">Mark</td>
										<td class="text-center">120.00</td>
										<td class="text-center">
											<input type="number" name="" value="01" style="max-width:50px">
										</td>
										<td class="text-right">120.00</td>
										<td class="text-right">
											<button class="btn btn-sm btn-danger" style="font-size: 12px;padding: 4px 8px;"><i class="fa fa-trash" aria-hidden="true"></i>
											</button>
										</td>
									</tr>
									<tr>
										<td class="text-left">Milk</td>
										<td class="text-center">80.00</td>
										<td class="text-center">
											<input type="number" name="" value="01" style="max-width:50px">
										</td>
										<td class="text-right">80.00</td>
										<td class="text-right">
											<button class="btn btn-sm btn-danger" style="font-size: 12px;padding: 4px 8px;"><i class="fa fa-trash" aria-hidden="true"></i>
											</button>
										</td>
									</tr>
									<tr>
										<td class="text-right" colspan="3">Sub Total</td>
										<td class="text-right">200.00</td>
										<td></td>
									</tr>
									<tr>
										<td class="text-right" colspan="3">Discount</td>
										<td class="text-right">
											<input type="number" name="" value="20" style="max-width:50px">
										</td>
										<td></td>
									</tr>
								</tbody>
								<tfoot>
									<tr class="table-success">
										<th scope="col" class="text-right" colspan="3">Total Price</th>
										<th scope="col" class="text-right">495.00</th>
										<td></td>
									</tr>
									<tr class="table-info">
										<th scope="col" colspan="4">
											<div class="btn-group">
												<button class="btn btn-danger">Cancel</button>
												<button class="btn btn-primary">Print Bill</button>
											</div>
										</th>
										<th scope="col">
											<div class="btn-group">
												<button class="btn btn-success">Payment</button>
											</div>
										</th>
									</tr>
								</tfoot>
							</table>
						</form>
					</div>
				</div>
			</div>
			<div class="col-lg-7 col-md-6 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="input-group">
                            <!-- <input type="text" class="form-control"> -->
                            <select class="custom-select select2">
                                <option selected disabled>Select Categories</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                            <select class="custom-select select2">
                                <option selected disabled>Sub Category</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                            <div class="input-group-prepend">
                                <!-- <span class="input-group-text" id="">Filter</span> -->
                                <button class="btn btn-success">Filter</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
                        <div class="row">
                            <div class="col-3">
                            <!-- small product box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <img src="{{ asset('front') }}/image/product/1.jpg" alt="Ground round enim" style="width: 100%">
                                    </div>
                                    <p class="product-title">Ground round enim</p>
                                    <a href="#" class="small-box-footer bg-success">
                                        Add to <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                            <!-- small product box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <img src="{{ asset('front') }}/image/product/2.jpg" alt="Ground round enim" style="width: 100%">
                                    </div>
                                    <p class="product-title">Ground round enim</p>
                                    <a href="#" class="small-box-footer bg-success">
                                        Add to <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                            <!-- small product box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <img src="{{ asset('front') }}/image/product/4.jpg" alt="Ground round enim" style="width: 100%">
                                    </div>
                                    <p class="product-title">Ground round enim</p>
                                    <a href="#" class="small-box-footer bg-success">
                                        Add to <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                            <!-- small product box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <img src="{{ asset('front') }}/image/product/2.jpg" alt="Ground round enim" style="width: 100%">
                                    </div>
                                    <p class="product-title">Ground round enim</p>
                                    <a href="#" class="small-box-footer bg-success">
                                        Add to <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                            <!-- small product box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <img src="{{ asset('front') }}/image/product/1.jpg" alt="Ground round enim" style="width: 100%">
                                    </div>
                                    <p class="product-title">Ground round enim</p>
                                    <a href="#" class="small-box-footer bg-success">
                                        Add to <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                            <!-- small product box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <img src="{{ asset('front') }}/image/product/3.jpg" alt="Ground round enim" style="width: 100%">
                                    </div>
                                    <p class="product-title">Ground round enim</p>
                                    <a href="#" class="small-box-footer bg-success">
                                        Add to <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                            <!-- small product box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <img src="{{ asset('front') }}/image/product/1.jpg" alt="Ground round enim" style="width: 100%">
                                    </div>
                                    <p class="product-title">Ground round enim</p>
                                    <a href="#" class="small-box-footer bg-success">
                                        Add to <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                            <!-- small product box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <img src="{{ asset('front') }}/image/product/2.jpg" alt="Ground round enim" style="width: 100%">
                                    </div>
                                    <p class="product-title">Ground round enim</p>
                                    <a href="#" class="small-box-footer bg-success">
                                        Add to <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<!-- start add new customer model -->
			<!-- Modal -->
			<div class="modal fade" id="add_customer_in_pos" tabindex="-1" role="dialog" aria-labelledby="add_customer_in_posTitle" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLongTitle">Create a Custoemr</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Name">
							</div>
							<div class="form-group">
								<input type="number" class="form-control" placeholder="Mobile Number">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary">Create Custoemr</button>
						</div>
					</div>
				</div>
			</div>
			<!-- end new customer model -->
		</div>
	</div>
</section>
<!-- / .min content  -->
@endsection
@push('script')
<!-- Select2 -->
<script src="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/js/select2.full.min.js"></script>
<script>
	$(function () {
	    //Initialize Select2 Elements
	    $('.select2').select2()
	})
</script>
@endpush
