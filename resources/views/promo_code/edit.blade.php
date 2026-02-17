@extends('layouts.app')

@section('title', 'Promo Code Edit')
@section('content')
@php
        $links = [
            'Home'=>route('dashboard'),
            'Loyalty Module'=>'',
            'Loyalty Entry'=>'',
            'Promo Code Edit'=>'',
        ]
    @endphp
    <x-breadcrumb title='Promo Code' :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Promo Code Edit</h4>
                            <div class="card-tools">
                                <a href="{{route('promo-codes.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{route('promo-codes.update',encrypt($row->id))}}" method="POST" class="" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-xl-3 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="code">Code</label>
                                            <input type="text" class="form-control" id="code"
                                                   name="code"
                                                   placeholder="Enter Promo Code" value="{{old('code', $row->code)}}" required>
                                            @if($errors->has('code'))
                                                <small class="text-danger">{{$errors->first('code')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="discount_type">Discount Type</label>
                                            <select name="discount_type" id="discount_type" class="form-control" required>
                                                <option value="" disabled selected>Please Select Type</option>
                                                <option value="fixed" {{ $row->discount_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                <option value="percentage"  {{ $row->discount_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                            </select>
                                            @if($errors->has('discount_type'))
                                                <small
                                                    class="text-danger">{{$errors->first('discount_type')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="discount_value">Discount Value</label>
                                            <input type="number" class="form-control" id="discount_value"
                                                   name="discount_value"
                                                   placeholder="Enter Discount Value"
                                                   value="{{old('discount_value', $row->discount_value)}}">
                                            @if($errors->has('discount_value'))
                                                <small
                                                    class="text-danger">{{$errors->first('discount_value')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="minimum_purchase">Minimum Purchase Amount</label>
                                            <input type="number" class="form-control" id="minimum_purchase"
                                                   name="minimum_purchase"
                                                   placeholder="Enter Purchase Amount"
                                                   value="{{old('minimum_purchase',$row->minimum_purchase) ?? 0}}">
                                            @if($errors->has('minimum_purchase'))
                                                <small
                                                    class="text-danger">{{$errors->first('minimum_purchase')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-3 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" name="start_date" id="start_date"
                                                   class="form-control" required value="{{ old('start_date', $row->start_date) }}">
                                            @if($errors->has('start_date'))
                                                <small class="text-danger">{{$errors->first('start_date')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control" required value="{{ old('start_date', $row->end_date) }}">
                                            @if($errors->has('end_date'))
                                                <small class="text-danger">{{$errors->first('end_date')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="end_date">Total Discount Amount</label>
                                            <input type="number" class="form-control" id="total_discount_amount"
                                                   name="total_discount_amount"
                                                   placeholder="Enter Total Discount Amount"
                                                   value="{{old('total_discount_amount',$row->total_discount_amount)}}">
                                            @if($errors->has('total_discount_amount'))
                                                <small
                                                    class="text-danger">{{$errors->first('total_discount_amount')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-3 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="discount_for">Discount For</label>
                                            <select name="discount_for" id="discount_for" class="form-control" required>
                                                <option value="" disabled selected>Please Select Type</option>
                                                <option value="all_customers" {{ $row->discount_for == 'all_customers' ? 'selected' : '' }}>All Customers</option>
                                                <option value="non_member" {{ $row->discount_for == 'non_member' ? 'selected' : '' }}>Non Member Customers</option>
                                                <option value="member" {{ $row->discount_for == 'member' ? 'selected' : '' }}>Member Customers</option>
                                            </select>
                                            @if($errors->has('discount_for'))
                                                <small
                                                    class="text-danger">{{$errors->first('discount_for')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-8 col-12 mb-1" id="memberDiv" hidden>
                                        <div class="form-group">
                                            <label for="member_type">Members</label>
                                            <select name="member_type[]" id="member_type" class="form-control select2"
                                                    multiple>
                                                <option value="" disabled>Please Select Member Type</option>
                                                @foreach($memberTypes as $memberType)
                                                    <option
                                                        value="{{ $memberType->id }}" {{ $row->member_types && in_array($memberType->id, explode(',',$row->member_types)) ? 'selected' : '' }}>{{ $memberType->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('member_type'))
                                                <small class="text-danger">{{$errors->first('member_type')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-8 col-12 mb-1" id="customerDiv" @if(!(count($row->customerPromoCodes) || in_array($row->discount_for, ['all_customers','non_member','member']))) hidden @endif>
                                        <div class="form-group">
                                            <label for="customers">Customers</label>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div></div>
                                                <button type="button" id="clearCustomersBtn" class="btn btn-sm btn-outline-secondary" style="{{ count($row->customerPromoCodes) ? 'display:inline-block;' : 'display:none;' }}">
                                                    <span id="clearSpinner" class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true" style="display:none;"></span>
                                                    Clear All
                                                </button>
                                             </div>
                                             <select name="customers[]" id="customers" class="form-control select2"
                                                    multiple>
                                                 @foreach($row->customerPromoCodes as $customerCode)
                                                 <option value="{{ $customerCode->id }}" selected>{{ $customerCode->customer->name ?? '' }} </option>
                                                 @endforeach
                                             </select>
                                             @if($errors->has('customers'))
                                                 <small class="text-danger">{{$errors->first('customers')}}</small>
                                             @endif
                                        </div>
                                    </div>
                                </div>
                                @if($row->start_date > date('Y-m-d'))
                                <button class="btn btn-primary waves-effect waves-float waves-light float-right" type="submit">Update</button>
                                @endif
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

@endsection
@section('js')

@endsection
@push('script')
    <script>
        // Global toggleClearButton so it is available to any function (including those
        // called during document ready like setPreValue/getDataByDiscount).
        function toggleClearButton() {
            const $customers = $('#customers');
            const $customerDiv = $('#customerDiv');
            const $clearBtn = $('#clearCustomersBtn');

            if ($customers.length === 0) return;

            let vals = $customers.val();
            let has = vals && vals.length > 0;

            if (!has) {
                has = $customers.find('option:selected').length > 0;
            }

            if (!has) {
                try {
                    const data = $customers.select2('data');
                    has = Array.isArray(data) && data.length > 0;
                } catch (e) {
                    // select2 may not be ready yet
                }
            }

            if (has) {
                $customerDiv.removeAttr('hidden');
                $clearBtn.show();
            } else {
                $clearBtn.hide();
            }
        }

         // cache jQuery selectors to avoid repeated DOM queries
         let $memberType, $customers, $discountFor, $memberDiv, $customerDiv, $clearBtn;

         $(document).ready(function () {
            $memberType = $('#member_type');
            $customers = $('#customers');
            $discountFor = $('#discount_for');
            $memberDiv = $('#memberDiv');
            $customerDiv = $('#customerDiv');
            $clearBtn = $('#clearCustomersBtn');

            $memberType.select2({
                placeholder: 'Please Select Member Type'
            });
            $customers.select2({
                placeholder: 'Please Select Customers',
                allowClear: true
            });

            // ensure toggle runs when select2 selection events happen (covers AJAX-loaded and user actions)
            $customers.on('select2:select select2:unselect select2:clear', function () {
                toggleClearButton();
            });

            // call toggle after initializing select2 to pick up pre-selected options
            setTimeout(toggleClearButton, 0);

            setPreValue("{{ $row->discount_for }}")

            $discountFor.on('change', function () {
                const value = $(this).val()
                switch (value) {
                    case 'all_customers':
                        $customerDiv.removeAttr('hidden')
                        $memberDiv.prop('hidden', true)
                        getDataByDiscount($customers, 'Select Customers')
                        break;
                    case 'non_member':
                        $customerDiv.removeAttr('hidden')
                        $memberDiv.prop('hidden', true)
                        getDataByDiscount($customers, 'Select Customers')
                        break;
                    case 'member':
                        $customerDiv.removeAttr('hidden')
                        $memberDiv.removeAttr('hidden')
                        getDataByDiscount($customers, 'Select Customers')
                        break;
                    default:
                        $memberDiv.prop('hidden', true)
                        $customerDiv.prop('hidden', true)
                }
                toggleClearButton();
            })

            $memberType.on('change', function (){
                getDataByDiscount($customers, 'Select Customers')
            })

            // show clear button when there are selected customers
            toggleClearButton();

            // when selection changes, show/hide clear button
            $customers.on('change', function (){
                toggleClearButton();
            });

            // clear all selected customers on button click with loader
            $clearBtn.on('click', function (){
                // show loader and disable the button to indicate progress
                $clearBtn.prop('disabled', true);
                $('#clearSpinner').show();

                // deselect all options and trigger select2 change
                $customers.val([]).trigger('change');
                // For safety with AJAX-backed select2, also clear any selected DOM options
                $customers.find('option:selected').prop('selected', false);

                // hide loader once the change event is observed (one-time listener)
                $customers.one('change', function (){
                    $('#clearSpinner').hide();
                    $clearBtn.prop('disabled', false);
                    toggleClearButton();
                });

                // fallback: ensure loader hidden even if no change event fires
                setTimeout(function (){
                    $('#clearSpinner').hide();
                    $clearBtn.prop('disabled', false);
                    toggleClearButton();
                }, 1000);
            });

            const field = document.querySelector('[name="code"]');
            if(field){
                field.addEventListener('keypress', function ( event ) {
                    const key = event.keyCode || event.which;
                    if (key === 32) {
                        event.preventDefault();
                    }
                });
            }
        })

        function getDataByDiscount(element, placeholder_text, no_result_message = "No Result Found") {
            const url = "{{route('promo-codes.customers')}}" + '?discount_for=' + $discountFor.val() + '&member_type[]='+($memberType.val())
            // element may be a jQuery object or selector string
            const $el = (element && element.jquery) ? element : $(element);
            $el.select2({
                placeholder: {
                    id: ' ', // the value of the option
                    text: placeholder_text
                },
                allowClear: true,
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 10,
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

            // ensure toggle runs after re-initializing select2 with AJAX results
            setTimeout(toggleClearButton, 0);
        }

        function setPreValue(value){
            switch (value) {
                case 'all_customers':
                    $customerDiv.removeAttr('hidden')
                    $memberDiv.prop('hidden', true)
                    getDataByDiscount($customers, 'Select Customers')
                    break;
                case 'non_member':
                    $customerDiv.removeAttr('hidden')
                    $memberDiv.prop('hidden', true)
                    getDataByDiscount($customers, 'Select Customers')
                    break;
                case 'member':
                    $customerDiv.removeAttr('hidden')
                    $memberDiv.removeAttr('hidden')
                    getDataByDiscount($customers, 'Select Customers')
                    break;
                default:
                    $memberDiv.prop('hidden', true)
                    $customerDiv.prop('hidden', true)
            }
            toggleClearButton();
        }
    </script>
@endpush
