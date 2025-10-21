@extends('layouts.app')
@section('title', 'Promo Code')
@section('content')
    @php
        $links = [
            'Home'=>route('dashboard'),
            'Loyalty Module'=>'',
            'Loyalty Entry'=>'',
            'Promo Code create'=>''
        ]
    @endphp
    <x-breadcrumb title='Promo Code create' :links="$links"/>
    <section class="content">
        <!-- Basic Inputs start -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Promo Code Create</h4>
                            <div class="card-tools">
                                <a href="{{route('promo-codes.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{route('promo-codes.store')}}" method="POST" class=""
                                  enctype="multipart/form-data">
                                @csrf

                                <!-- Promo Code Basic Info Section -->
                                <h5 class="mb-3 font-weight-bold">Basic Information</h5>
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="code">Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="code"
                                                   name="code"
                                                   placeholder="e.g., SAVE20" value="{{old('code')}}" required>
                                            @if($errors->has('code'))
                                                <small class="text-danger">{{$errors->first('code')}}</small>
                                            @endif
                                            <small class="form-text text-muted">No spaces allowed</small>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="discount_type">Discount Type <span class="text-danger">*</span></label>
                                            <select name="discount_type" id="discount_type" class="form-control" required>
                                                <option value="" disabled selected>Select discount type</option>
                                                <option value="fixed">Fixed Amount</option>
                                                <option value="percentage">Percentage (%)</option>
                                            </select>
                                            @if($errors->has('discount_type'))
                                                <small class="text-danger">{{$errors->first('discount_type')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="discount_value">Discount Value <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="discount_value"
                                                   name="discount_value" step="0.01" min="0"
                                                   placeholder="Enter value"
                                                   value="{{old('discount_value')}}" required>
                                            @if($errors->has('discount_value'))
                                                <small class="text-danger">{{$errors->first('discount_value')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="minimum_purchase">Minimum Purchase Amount</label>
                                            <input type="number" class="form-control" id="minimum_purchase"
                                                   name="minimum_purchase" step="0.01" min="0"
                                                   placeholder="Optional"
                                                   value="{{old('minimum_purchase') ?? 0}}">
                                            @if($errors->has('minimum_purchase'))
                                                <small class="text-danger">{{$errors->first('minimum_purchase')}}</small>
                                            @endif
                                            <small class="form-text text-muted">Leave 0 for no minimum</small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Validity Period Section -->
                                <h5 class="mb-3 font-weight-bold">Validity Period</h5>
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                            <input type="date" name="start_date" id="start_date"
                                                   class="form-control" value="{{old('start_date')}}" required>
                                            @if($errors->has('start_date'))
                                                <small class="text-danger">{{$errors->first('start_date')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="end_date">End Date <span class="text-danger">*</span></label>
                                            <input type="date" name="end_date" id="end_date"
                                                   class="form-control" value="{{old('end_date')}}" required>
                                            @if($errors->has('end_date'))
                                                <small class="text-danger">{{$errors->first('end_date')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Target Customers Section -->
                                <h5 class="mb-3 font-weight-bold">Target Customers</h5>
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="discount_for">Discount For <span class="text-danger">*</span></label>
                                            <select name="discount_for" id="discount_for" class="form-control" required>
                                                <option value="" disabled selected>Select customer type</option>
                                                <option value="all_customers">All Customers</option>
                                                <option value="non_member">Non-Member Customers</option>
                                                <option value="member">Member Customers</option>
                                            </select>
                                            @if($errors->has('discount_for'))
                                                <small class="text-danger">{{$errors->first('discount_for')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 col-12 mb-3" id="memberDiv" style="display: none;">
                                        <div class="form-group">
                                            <label for="member_type">Member Types</label>
                                            <select name="member_type[]" id="member_type" class="form-control select2"
                                                    multiple>
                                                @foreach($memberTypes as $memberType)
                                                    <option value="{{ $memberType->id }}">{{ $memberType->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('member_type'))
                                                <small class="text-danger">{{$errors->first('member_type')}}</small>
                                            @endif
                                            <small class="form-text text-muted">Optional: Leave empty for all members</small>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-12 col-12 mb-3" id="customerDiv" style="display: none;">
                                        <div class="form-group">
                                            <label for="customers">Specific Customers</label>
                                            <select name="customers[]" id="customers" class="form-control select2"
                                                    multiple>
                                            </select>
                                            @if($errors->has('customers'))
                                                <small class="text-danger">{{$errors->first('customers')}}</small>
                                            @endif
                                            <small class="form-text text-muted">Optional: Leave empty for all in selected category</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button class="btn btn-info waves-effect waves-float waves-light float-right px-4"
                                                type="submit">
                                            <i class="fa fa-check mr-1"></i> Submit
                                        </button>
                                        <a href="{{route('promo-codes.index')}}"
                                           class="btn btn-secondary waves-effect float-right mr-2 px-4">
                                            <i class="fa fa-times mr-1"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Basic Inputs end -->
    </section>

@endsection
@section('css')
    <style>
        .form-group label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .form-control:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
        }
        .select2-container .select2-selection--multiple {
            min-height: 38px;
        }
        hr {
            border-top: 1px solid rgba(0,0,0,0.1);
        }
    </style>
@endsection
@section('js')

@endsection
@push('script')
    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#member_type').select2({
                placeholder: 'Select member types',
                allowClear: true
            });
            $('#customers').select2({
                placeholder: 'Select customers',
                allowClear: true
            });

            // Handle discount_for change
            $('#discount_for').on('change', function () {
                const value = $(this).val();

                // Clear all selections when discount type changes
                $('#member_type').val(null).trigger('change');
                $('#customers').val(null).trigger('change');

                // Clear and destroy customers select2 to reset it completely
                $('#customers').empty().trigger('change');

                // Hide all conditional divs first
                $('#memberDiv').hide();
                $('#customerDiv').hide();

                // Show relevant divs based on selection
                switch (value) {
                    case 'all_customers':
                        $('#customerDiv').show();
                        getDataByDiscount('#customers', 'Select specific customers (optional)');
                        break;
                    case 'non_member':
                        $('#customerDiv').show();
                        getDataByDiscount('#customers', 'Select specific customers (optional)');
                        break;
                    case 'member':
                        $('#memberDiv').show();
                        $('#customerDiv').show();
                        getDataByDiscount('#customers', 'Select specific customers (optional)');
                        break;
                    default:
                        break;
                }
            });

            // Handle member_type change
            $('#member_type').on('change', function () {
                // Clear customer selection when member type changes
                $('#customers').val(null).trigger('change');
                $('#customers').empty().trigger('change');

                // Reload customers based on new member type selection
                getDataByDiscount('#customers', 'Select specific customers (optional)');
            });

            // Prevent spaces in promo code
            const field = document.querySelector('[name="code"]');
            field.addEventListener('keypress', function (event) {
                const key = event.keyCode;
                if (key === 32) {
                    event.preventDefault();
                }
            });

            // Convert code to uppercase automatically
            field.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Set minimum date for start_date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').setAttribute('min', today);

            // Update end_date minimum when start_date changes
            $('#start_date').on('change', function() {
                const startDate = $(this).val();
                $('#end_date').attr('min', startDate);

                // Clear end_date if it's before the new start_date
                const endDate = $('#end_date').val();
                if (endDate && endDate < startDate) {
                    $('#end_date').val('');
                }
            });
        });

        function getDataByDiscount(element, placeholder_text, no_result_message = "No customers found") {
            const discountFor = $('#discount_for').val();
            const memberTypes = $('#member_type').val() || [];

            if (!discountFor) return;

            const url = "{{route('promo-codes.customers')}}" +
                '?discount_for=' + discountFor +
                '&member_type[]=' + memberTypes.join('&member_type[]=');

            $(element).select2({
                placeholder: {
                    id: '',
                    text: placeholder_text
                },
                allowClear: true,
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                },
                language: {
                    noResults: function () {
                        return no_result_message;
                    }
                },
            });
        }
    </script>
@endpush
