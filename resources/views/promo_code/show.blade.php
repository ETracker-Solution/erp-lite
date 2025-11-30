@extends('layouts.app')

@section('title', 'Promo Code Details')
@section('content')
    @php
        $links = [
            'Home'=>route('dashboard'),
            'Loyalty Module'=>'',
            'Loyalty Entry'=>'',
            'Promo Codes'=>route('promo-codes.index'),
            'Details'=>''
        ]
    @endphp
    <x-breadcrumb title='Promo Code Details' :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Promo Code: <strong>{{ $row->code }}</strong></h4>
                            <div class="card-tools">
                                @if($row->start_date > date('Y-m-d'))
                                    <a href="{{ route('promo-codes.edit', encrypt($row->id)) }}" class="btn btn-sm btn-warning mr-1">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                @endif
                                <a href="{{ route('promo-codes.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> See List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Status Badge -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    @php
                                        $today = date('Y-m-d');
                                        $status = '';
                                        $badgeClass = '';

                                        if ($row->start_date > $today) {
                                            $status = 'Upcoming';
                                            $badgeClass = 'badge-info';
                                        } elseif ($row->end_date < $today) {
                                            $status = 'Expired';
                                            $badgeClass = 'badge-secondary';
                                        } else {
                                            $status = 'Active';
                                            $badgeClass = 'badge-success';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }} badge-lg px-3 py-2" style="font-size: 16px;">
                                    <i class="fa fa-circle mr-1"></i> {{ $status }}
                                </span>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <h5 class="mb-3 font-weight-bold border-bottom pb-2">
                                <i class="fa fa-info-circle text-info"></i> Basic Information
                            </h5>
                            <div class="row mb-4">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="text-muted">Promo Code</span>
                                            <h5 class="font-weight-bold text-primary mb-0">{{ $row->code }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="text-muted">Discount Type</span>
                                            <h5 class="font-weight-bold mb-0">
                                                @if($row->discount_type == 'fixed')
                                                    <span class="badge badge-primary">Fixed Amount</span>
                                                @else
                                                    <span class="badge badge-warning">Percentage</span>
                                                @endif
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="text-muted">Discount Value</span>
                                            <h5 class="font-weight-bold text-success mb-0">
                                                @if($row->discount_type == 'percentage')
                                                    {{ $row->discount_value }}%
                                                @else
                                                    ৳{{ number_format($row->discount_value, 2) }}
                                                @endif
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="text-muted">Minimum Purchase</span>
                                            <h5 class="font-weight-bold mb-0">
                                                @if($row->minimum_purchase > 0)
                                                    ৳{{ number_format($row->minimum_purchase, 2) }}
                                                @else
                                                    <span class="text-muted">No Minimum</span>
                                                @endif
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Validity Period -->
                            <h5 class="mb-3 font-weight-bold border-bottom pb-2">
                                <i class="fa fa-calendar-alt text-info"></i> Validity Period
                            </h5>
                            <div class="row mb-4">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <small class="text-muted d-block">Start Date</small>
                                            <h6 class="font-weight-bold mb-0">
                                                <i class="fa fa-calendar-check text-success"></i>
                                                {{ \Carbon\Carbon::parse($row->start_date)->format('d M, Y') }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <small class="text-muted d-block">End Date</small>
                                            <h6 class="font-weight-bold mb-0">
                                                <i class="fa fa-calendar-times text-danger"></i>
                                                {{ \Carbon\Carbon::parse($row->end_date)->format('d M, Y') }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <small class="text-muted d-block">Duration</small>
                                            <h6 class="font-weight-bold mb-0">
                                                <i class="fa fa-hourglass-half text-warning"></i>
                                                {{ \Carbon\Carbon::parse($row->start_date)->diffInDays(\Carbon\Carbon::parse($row->end_date)) + 1 }} Days
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <small class="text-muted d-block">Max Use Per User</small>
                                            <h6 class="font-weight-bold mb-0">
                                                <i class="fa fa-clock text-warning"></i>
                                                {{ $row->max_use_per_user }} Time
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Target Customers -->
                            <h5 class="mb-3 font-weight-bold border-bottom pb-2">
                                <i class="fa fa-users text-info"></i> Target Customers
                            </h5>
                            <div class="row mb-4">
                                <div class="col-md-4 col-sm-6 mb-3">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <small class="text-muted d-block">Customer Type</small>
                                            <h6 class="font-weight-bold mb-0">
                                                @if($row->discount_for == 'all_customers')
                                                    <span class="badge badge-primary">All Customers</span>
                                                @elseif($row->discount_for == 'non_member')
                                                    <span class="badge badge-secondary">Non-Member</span>
                                                @else
                                                    <span class="badge badge-success">Member Only</span>
                                                @endif
                                            </h6>
                                        </div>
                                    </div>
                                </div>

                                @if($row->discount_for == 'member' && $row->member_types)
                                    <div class="col-md-8 col-sm-12 mb-3">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <small class="text-muted d-block">Member Types</small>
                                                <div class="mt-2">
                                                    @php
                                                        $memberTypeIds = explode(',', $row->member_types);
                                                        $memberTypes = \App\Models\MembershipType::whereIn('id', $memberTypeIds)->get();
                                                    @endphp
                                                    @foreach($memberTypes as $type)
                                                        <span class="badge badge-info mr-1 mb-1">{{ $type->name }}</span>
                                                    @endforeach
                                                    @if($memberTypes->isEmpty())
                                                        <span class="text-muted">All Member Types</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Specific Customers -->
                            @if($row->customerPromoCodes->isNotEmpty())
                                <h5 class="mb-3 font-weight-bold border-bottom pb-2">
                                    <i class="fa fa-user-check text-info"></i> Specific Customers ({{ $row->customerPromoCodes->count() }})
                                </h5>
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered">
                                                <thead class="bg-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="30%">Name</th>
                                                    <th width="20%">Mobile</th>
                                                    <th width="25%">Email</th>
                                                    <th width="20%">Member Type</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($row->customerPromoCodes as $index => $customerCode)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $customerCode->customer->name ?? 'N/A' }}</td>
                                                        <td>{{ $customerCode->customer->mobile ?? 'N/A' }}</td>
                                                        <td>{{ $customerCode->customer->email ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($customerCode->customer->membership_type)
                                                                <span class="badge badge-info">
                                                                {{ $customerCode->customer->membership_type->name }}
                                                            </span>
                                                            @else
                                                                <span class="text-muted">Non-Member</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> This promo code is available to all customers in the selected category.
                                </div>
                            @endif

                            <!-- SMS Template -->
                            @if($row->smsTemplate)
                                <h5 class="mb-3 font-weight-bold border-bottom pb-2">
                                    <i class="fa fa-sms text-info"></i> SMS Notification
                                </h5>
                                <div class="row mb-4">
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <small class="text-muted d-block">Template Name</small>
                                                <h6 class="font-weight-bold mb-0">{{ $row->smsTemplate->template_name }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-12 mb-3">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <small class="text-muted d-block mb-2">Message Preview</small>
                                                <div class="border rounded p-3 bg-white" style="white-space: pre-wrap; line-height: 1.6;">
                                                    {{ $row->smsTemplate->message_template }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i> No SMS template configured for this promo code.
                                </div>
                            @endif

                            <!-- Usage Statistics (Optional - if you track usage) -->
                            @if(isset($row->usage_count))
                                <h5 class="mb-3 font-weight-bold border-bottom pb-2">
                                    <i class="fa fa-chart-bar text-info"></i> Usage Statistics
                                </h5>
                                <div class="row mb-4">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="info-box bg-primary">
                                            <div class="info-box-content text-white">
                                                <span>Total Uses</span>
                                                <h4 class="font-weight-bold mb-0">{{ $row->usage_count ?? 0 }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="info-box bg-success">
                                            <div class="info-box-content text-white">
                                                <span>Total Discount Given</span>
                                                <h4 class="font-weight-bold mb-0">৳{{ number_format($row->total_discount_given ?? 0, 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="info-box bg-warning">
                                            <div class="info-box-content text-white">
                                                <span>Total Revenue</span>
                                                <h4 class="font-weight-bold mb-0">৳{{ number_format($row->total_revenue ?? 0, 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="info-box bg-info">
                                            <div class="info-box-content text-white">
                                                <span>Unique Customers</span>
                                                <h4 class="font-weight-bold mb-0">{{ $row->unique_customers ?? 0 }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Metadata -->
                            <h5 class="mb-3 font-weight-bold border-bottom pb-2">
                                <i class="fa fa-clock text-info"></i> Record Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <small class="text-muted d-block">Created At</small>
                                            <h6 class="mb-0">
                                                {{ \Carbon\Carbon::parse($row->created_at)->format('d M, Y h:i A') }}
                                            </h6>
                                            @if($row->created_by)
                                                <small class="text-muted">by {{ $row->creator->name ?? 'System' }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <small class="text-muted d-block">Last Updated</small>
                                            <h6 class="mb-0">
                                                {{ \Carbon\Carbon::parse($row->updated_at)->format('d M, Y h:i A') }}
                                            </h6>
                                            @if($row->updated_by)
                                                <small class="text-muted">by {{ $row->updater->name ?? 'System' }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12 text-right">
                                    @if($row->start_date > date('Y-m-d'))
                                        <a href="{{ route('promo-codes.edit', encrypt($row->id)) }}" class="btn btn-warning">
                                            <i class="fa fa-edit"></i> Edit Promo Code
                                        </a>
                                    @endif
                                    <a href="{{ route('promo-codes.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <style>
        .info-box {
            border-radius: 8px;
            padding: 15px;
            height: 100%;
        }
        .info-box-content span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .card {
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .badge-lg {
            font-size: 16px;
        }
        .border-bottom {
            border-bottom: 2px solid #e9ecef !important;
        }
        .table thead th {
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
@endsection
