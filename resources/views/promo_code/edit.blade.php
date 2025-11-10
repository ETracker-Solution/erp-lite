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
    <x-breadcrumb title='Promo Code Edit' :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Promo Code Edit</h4>
                            <div class="card-tools">
                                <a href="{{route('promo-codes.index')}}">
                                    <button class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i> See List
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{route('promo-codes.update',encrypt($row->id))}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('put')

                                <!-- Promo Code Basic Info Section -->
                                <h5 class="mb-3 font-weight-bold">Basic Information</h5>
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="code">Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="code"
                                                   name="code"
                                                   placeholder="e.g., SAVE20"
                                                   value="{{old('code', $row->code)}}"
                                                   required>
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
                                                <option value="" disabled>Select discount type</option>
                                                <option value="fixed" {{ old('discount_type', $row->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                                <option value="percentage" {{ old('discount_type', $row->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
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
                                                   value="{{old('discount_value', $row->discount_value)}}"
                                                   required>
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
                                                   value="{{old('minimum_purchase', $row->minimum_purchase) ?? 0}}">
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
                                                   class="form-control"
                                                   value="{{old('start_date', $row->start_date)}}"
                                                   required>
                                            @if($errors->has('start_date'))
                                                <small class="text-danger">{{$errors->first('start_date')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="end_date">End Date <span class="text-danger">*</span></label>
                                            <input type="date" name="end_date" id="end_date"
                                                   class="form-control"
                                                   value="{{old('end_date', $row->end_date)}}"
                                                   required>
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
                                                <option value="" disabled>Select customer type</option>
                                                <option value="all_customers" {{ old('discount_for', $row->discount_for) == 'all_customers' ? 'selected' : '' }}>All Customers</option>
                                                <option value="non_member" {{ old('discount_for', $row->discount_for) == 'non_member' ? 'selected' : '' }}>Non-Member Customers</option>
                                                <option value="member" {{ old('discount_for', $row->discount_for) == 'member' ? 'selected' : '' }}>Member Customers</option>
                                            </select>
                                            @if($errors->has('discount_for'))
                                                <small class="text-danger">{{$errors->first('discount_for')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 col-12 mb-3" id="memberDiv" style="display: none;">
                                        <div class="form-group">
                                            <label for="member_type">Member Types</label>
                                            <select name="member_type[]" id="member_type" class="form-control select2" multiple>
                                                @foreach($memberTypes as $memberType)
                                                    <option value="{{ $memberType->id }}"
                                                        {{ $row->member_types && in_array($memberType->id, explode(',', $row->member_types)) ? 'selected' : '' }}>
                                                        {{ $memberType->name }}
                                                    </option>
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
                                            <select name="customers[]" id="customers" class="form-control select2" multiple>
                                                @foreach($row->customerPromoCodes as $customerCode)
                                                    <option value="{{ $customerCode->customer_id }}" selected>
                                                        {{ $customerCode->customer->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('customers'))
                                                <small class="text-danger">{{$errors->first('customers')}}</small>
                                            @endif
                                            <small class="form-text text-muted">Optional: Leave empty for all in selected category</small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- SMS Configuration Section -->
                                <h5 class="mb-3 font-weight-bold">SMS Notification</h5>
                                <div class="row">
                                    <div class="col-xl-4 col-md-6 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="sms_template_id">SMS Template</label>
                                            <select name="sms_template_id" id="sms_template_id" class="form-control">
                                                <option value="">Select a Template</option>
                                                @foreach($smsTemplates as $template)
                                                    <option value="{{ $template->id }}"
                                                            {{ old('sms_template_id', $row->sms_template_id) == $template->id ? 'selected' : '' }}
                                                            data-preview="{{ $template->message_template }}"
                                                            data-labels="{{ json_encode($template->getVariableLabels()) }}">
                                                        {{ $template->template_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('sms_template_id'))
                                                <small class="text-danger">{{$errors->first('sms_template_id')}}</small>
                                            @endif
                                            <small class="form-text text-muted">
                                                <a href="#" id="refreshTemplates" class="text-info">
                                                    <i class="fa fa-sync-alt"></i> Refresh templates
                                                </a>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-xl-8 col-md-12 col-12 mb-3">
                                        <div class="form-group">
                                            <label>Template Preview</label>
                                            <div id="templatePreview" class="border rounded p-3 bg-light" style="min-height: 120px; white-space: pre-wrap; line-height: 1.6;">
                                                <em class="text-muted">Select a template to preview the message</em>
                                            </div>
                                            <small class="form-text text-muted" id="varHelp" style="display: none;">
                                                <strong>Variables:</strong> <span id="varList"></span>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        @if($row->start_date > date('Y-m-d'))
                                            <button class="btn btn-info waves-effect waves-float waves-light float-right px-4" type="submit">
                                                <i class="fa fa-check mr-1"></i> Update
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-secondary float-right px-4" disabled>
                                                <i class="fa fa-lock mr-1"></i> Cannot Edit (Already Started)
                                            </button>
                                        @endif
                                        <a href="{{route('promo-codes.index')}}" class="btn btn-secondary waves-effect float-right mr-2 px-4">
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
        #templatePreview {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }
        #templatePreview .var-highlight {
            background-color: #fff3cd;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
            color: #856404;
            border: 1px dashed #ffc107;
        }
        #varList {
            font-family: monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
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

            // Set initial visibility based on saved value
            setPreValue("{{ old('discount_for', $row->discount_for) }}");

            // Handle discount_for change
            $('#discount_for').on('change', function () {
                const value = $(this).val();

                // Clear all selections when discount type changes
                $('#member_type').val(null).trigger('change');

                // Clear and destroy customers select2 but keep existing selected values
                const existingValues = $('#customers').val();
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

            // Trigger template preview on load if template is selected
            if ($('#sms_template_id').val()) {
                $('#sms_template_id').trigger('change');
            }
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

        function setPreValue(value) {
            switch (value) {
                case 'all_customers':
                    $('#customerDiv').show();
                    $('#memberDiv').hide();
                    getDataByDiscount('#customers', 'Select specific customers (optional)');
                    break;
                case 'non_member':
                    $('#customerDiv').show();
                    $('#memberDiv').hide();
                    getDataByDiscount('#customers', 'Select specific customers (optional)');
                    break;
                case 'member':
                    $('#customerDiv').show();
                    $('#memberDiv').show();
                    getDataByDiscount('#customers', 'Select specific customers (optional)');
                    break;
                default:
                    $('#memberDiv').hide();
                    $('#customerDiv').hide();
            }
        }

        function getSampleValues() {
            const code = $('#code').val() || 'PROMO123';
            const discountType = $('#discount_type').val();
            const discountValue = $('#discount_value').val() || '20';
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            // Format discount
            let discount = discountType === 'percentage'
                ? discountValue + '%'
                : '৳' + discountValue;

            // Format dates
            const formatDate = (dateStr) => {
                if (!dateStr) return new Date().toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'});
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'});
            };

            return [
                code,
                discount,
                formatDate(startDate),
                formatDate(endDate)
            ];
        }

        function replaceVariablesWithHighlight(template, values) {
            let result = template;
            let index = 0;

            while (result.includes('{#var#}') && index < values.length) {
                result = result.replace('{#var#}',
                    '<span class="var-highlight">' + values[index] + '</span>'
                );
                index++;
            }

            result = result.replace(/{#var#}/g,
                '<span class="var-highlight text-muted">N/A</span>'
            );

            return result;
        }

        $('#sms_template_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const template = selectedOption.data('preview');
            const labels = selectedOption.data('labels');

            if (template) {
                const sampleValues = getSampleValues();
                const previewHtml = replaceVariablesWithHighlight(template, sampleValues);

                $('#templatePreview').html(previewHtml);

                if (labels && Array.isArray(labels) && labels.length > 0) {
                    let varListHtml = labels.map((label, idx) =>
                        `{#var${idx + 1}#} = ${label}`
                    ).join(', ');

                    $('#varList').html(varListHtml);
                    $('#varHelp').show();
                } else {
                    $('#varHelp').hide();
                }
            } else {
                $('#templatePreview').html('<em class="text-muted">Select a template to preview the message</em>');
                $('#varHelp').hide();
            }
        });

        $('#code, #discount_type, #discount_value, #start_date, #end_date').on('change input', function() {
            if ($('#sms_template_id').val()) {
                $('#sms_template_id').trigger('change');
            }
        });

        $('#refreshTemplates').on('click', function(e) {
            e.preventDefault();
            const $link = $(this);
            const originalText = $link.html();

            $link.html('<i class="fa fa-spinner fa-spin"></i> Syncing...');
            $link.addClass('disabled');

            $.ajax({
                url: '{{ route("promo-codes.fetchTemplates") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('Templates synced successfully! Reloading...');
                    location.reload();
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to sync templates. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                    $link.html(originalText);
                    $link.removeClass('disabled');
                }
            });
        });
    </script>
@endpush
