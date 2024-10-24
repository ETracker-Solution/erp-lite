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
                                    <div class="col-xl-3 col-md-8 col-12 mb-1" id="customerDiv" hidden>
                                        <div class="form-group">
                                            <label for="customers">Customers</label>
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
        </d>
        <!-- Basic Inputs end -->
    </section>

@endsection
@section('css')

@endsection
@section('js')

@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $('#member_type').select2({
                placeholder: 'Please Select Member Type'
            });
            $('#customers').select2({
                placeholder: 'Please Select Customers'
            });

            setPreValue("{{ $row->discount_for }}")

            $('#discount_for').on('change', function () {
                const value = $(this).val()
                switch (value) {
                    case 'all_customers':
                        $('#customerDiv').removeAttr('hidden')
                        $('#memberDiv').prop('hidden', true)
                        getDataByDiscount('#customers', 'Select Customers')
                    case 'non_member':
                        $('#customerDiv').removeAttr('hidden')
                        $('#memberDiv').prop('hidden', true)
                        getDataByDiscount('#customers', 'Select Customers')
                        break;
                    case 'member':
                        $('#customerDiv').removeAttr('hidden')
                        $('#memberDiv').removeAttr('hidden')
                        getDataByDiscount('#customers', 'Select Customers')
                        break;
                    default:
                        $('#memberDiv').prop('hidden', true)
                        $('#customerDiv').prop('hidden', true)
                }
            })
            $('#member_type').on('change', function (){
                getDataByDiscount('#customers', 'Select Customers')
            })

            const field = document.querySelector('[name="code"]');

            field.addEventListener('keypress', function ( event ) {
                const key = event.keyCode;
                if (key === 32) {
                    event.preventDefault();
                }
            });
        })

        function getDataByDiscount(element, placeholder_text, no_result_message = "No Result Found") {
            const url = "{{route('promo-codes.customers')}}" + '?discount_for=' + $('#discount_for').val() + '&member_type[]='+($('#member_type').val())
            $(element).select2({
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
        }

        function setPreValue(value){
            switch (value) {
                case 'all_customers':
                    $('#customerDiv').removeAttr('hidden')
                    $('#memberDiv').prop('hidden', true)
                    getDataByDiscount('#customers', 'Select Customers')
                case 'non_member':
                    $('#customerDiv').removeAttr('hidden')
                    $('#memberDiv').prop('hidden', true)
                    getDataByDiscount('#customers', 'Select Customers')
                    break;
                case 'member':
                    $('#customerDiv').removeAttr('hidden')
                    $('#memberDiv').removeAttr('hidden')
                    getDataByDiscount('#customers', 'Select Customers')
                    break;
                default:
                    $('#memberDiv').prop('hidden', true)
                    $('#customerDiv').prop('hidden', true)
            }
        }
    </script>
@endpush
