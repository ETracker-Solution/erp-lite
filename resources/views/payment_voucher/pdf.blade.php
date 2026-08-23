<!DOCTYPE html>
<html>
<head>
    <title>Payment Voucher</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }

        body {
            font-size: 12px;
        }

        .meta td {
            padding: 4px 0;
            vertical-align: top;
        }

        .entries {
            border-collapse: collapse;
            width: 100%;
            margin-top: 12px;
        }

        .entries th,
        .entries td {
            border: 1px solid #333;
            padding: 6px 8px;
        }

        .entries th {
            background: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .muted {
            color: #555;
            font-size: 10px;
        }
    </style>
</head>
<body>
@php
    $amountFmt = number_format((float) $paymentVoucher->amount, 2);
@endphp
<div>
    @include('common.pdf_header')
</div>

<p style="text-align:center; font-size:18px; margin:8px 0 4px;"><strong>Payment Voucher</strong></p>
<p style="text-align:center; margin:0 0 10px;">{{ $paymentVoucher->uid ?: ('#'.$paymentVoucher->id) }}</p>
<hr>

<table width="100%" class="meta">
    <tr>
        <td width="50%"><strong>Date:</strong> {{ $paymentVoucher->date }}</td>
        <td width="50%"><strong>Amount:</strong> {{ $amountFmt }}</td>
    </tr>
    <tr>
        <td><strong>Paid To:</strong> {{ $paymentVoucher->payee_name ?: '—' }}</td>
        <td><strong>Reference:</strong> {{ $paymentVoucher->reference_no ?: '—' }}</td>
    </tr>
</table>

<table class="entries">
    <thead>
    <tr>
        <th width="8%" style="text-align:center;">#</th>
        <th>Particulars</th>
        <th width="18%" class="text-right">Debit</th>
        <th width="18%" class="text-right">Credit</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="text-align:center;">1</td>
        <td>
            {{ $paymentVoucher->debitAccount->name ?? '—' }}
            <div class="muted">Dr — Debit Account (Expense / Payable)</div>
        </td>
        <td class="text-right">{{ $amountFmt }}</td>
        <td class="text-right">—</td>
    </tr>
    <tr>
        <td style="text-align:center;">2</td>
        <td>
            {{ $paymentVoucher->cashBankAccount->name ?? '—' }}
            <div class="muted">Cr — Payment Account (Cash / Bank)</div>
        </td>
        <td class="text-right">—</td>
        <td class="text-right">{{ $amountFmt }}</td>
    </tr>
    <tr>
        <th colspan="2" class="text-right">Total</th>
        <th class="text-right">{{ $amountFmt }}</th>
        <th class="text-right">{{ $amountFmt }}</th>
    </tr>
    </tbody>
</table>

<p style="margin-top:12px;"><strong>Amount in Words:</strong> {{ $paymentVoucher->amountInWords() }}</p>
<p><strong>Narration:</strong> {{ $paymentVoucher->narration ?: '—' }}</p>

<htmlpagefooter name="page-footer">
    <table width="100%" style="margin-top:30px;">
        <tr>
            <td style="text-align:left; width:33%;"><span style="border-top:1px solid #000;">Prepared By</span></td>
            <td style="text-align:center; width:33%;"><span style="border-top:1px solid #000;">Checked By</span></td>
            <td style="text-align:right; width:33%;"><span style="border-top:1px solid #000;">Approved By</span></td>
        </tr>
    </table>
    <hr>
    <strong style="font-size:9px;">
        Printing Time:- {{ now('Asia/Dhaka')->format('F j, Y, g:i a') }}
    </strong>
</htmlpagefooter>
</body>
</html>
