@extends('layouts.app')

@section('title', 'Journal Voucher Details')
@section('content')
@push('style')

@endpush
@php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'General Accounts'=>'',
    'Journal Voucher Details'=>'',
    ]
    @endphp
    <x-breadcrumb title='Journal Voucher Details' :links="$links"/>
     <!-- Basic Inputs start -->
     <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Journal Voucher Details</h4>
                            <div class="card-tools">
                                <a href="{{route('journal-vouchers.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong>Date :</strong></th>
                                        <td>{{ $journalVoucher->date }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>JV No :</strong></th>
                                        <td>{{ $journalVoucher->uid }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Debit Account  :</strong></th>
                                        <td>{{ $journalVoucher->debitAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Credit Account  :</strong></th>
                                        <td>{{ $journalVoucher->creditAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Amount :</strong></th>
                                        <td>{{ $journalVoucher->amount }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Description :</strong></th>
                                        <td>{{ $journalVoucher->narration }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Referance :</strong></th>
                                        <td>{{ $journalVoucher->reference_no }}</td>
                                    </tr>
                                    
                                </thead>
                            </table>
                        </div>
                        {{-- adjust modal --}}
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->

@endsection
@section('css')

@endsection
@section('js')

@endsection