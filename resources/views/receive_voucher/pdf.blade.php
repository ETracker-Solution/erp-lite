<!DOCTYPE html>
<html>

<head>
    <title>Receive Voucher </title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }

        body {
            margin-top: 20px;
        }

        .textRight{
            text-align: right;
        }
        table {
            width: 95%;
            margin: 0 auto;
            border-spacing: 0;
            border-collapse: separate;
        }

        td {
            padding: 5px;
        }

        .border {
            border: 1px solid black;
        }

        .border-left {
            border-left: 1px solid black;
        }

        .border-top {
            border-top: 1px solid black;
        }

        .border-bottom {
            border-bottom: 1px solid black;
        }

        .border-bottom-thick {
            border-bottom: 2px solid black;
        }

        .bold {
            font-weight: bold;
        }

        .spacer {
            height: 250px;
        }

        .detailsDiv {
            justify-content: space-between;
        }

    </style>
</head>

<body>
<div class="invoice-ribbon">
    @include('common.pdf_header')
</div>
<p style="text-align:center;  font-size: 20px;">Receive Voucher</p>
<table>
    <tr>
        <td>No. : <span class="bold">{{ $voucher->uid }}</span></td>
        <td class="textRight">Dated : <span class="bold">{{ $voucher->date }}</span></td>
    </tr>
</table>
<br><br>
<table>
    <thead>
    <tr>
        <td class="bold border-top border-bottom">Particulars</td>
        <td class="textRight border border-top">Amount</td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="bold">Account:</td>
        <td class="border-left">&nbsp;</td>
    </tr>
    @php
        $totalAmount = 0;
    @endphp
    @foreach($receiveVouchers as $item)
        @php
            $totalAmount += $item->amount;
        @endphp
        <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $item->creditAccount->name }} ({{ $item->debitAccount->name }} - {{ $item->payee_name }} )</td>
            <td class="textRight border-left">{{commaSeperated($item->amount)}}</td>
        </tr>
    @endforeach
    <tr>
        <td class="spacer" style="height: 50px;"></td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td class="bold">On Account Of:</td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $voucher->narration }}.</td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td class="bold">Amount (in words):</td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bangladeshi Taka {{ numberToWords($totalAmount) }} Only</td>
        <td class="textRight border-left border-top border-bottom-thick">TK {{ commaSeperated($totalAmount) }}</td>
    </tr>
    </tbody>
</table>
<br>
<br>
<br>
<br>
<table>
    <tr>
        <td>Receiver’s Signature. : </td>
        <td>Authorised Signatory : </td>
    </tr>
</table>
<br>
<br>
<br>
<br>
<table>
    <tr>
        <td>Prepared by </td>
        <td>Checked by </td>
        <td>Verified by </td>
    </tr>
</table>
{{--<table width="100%" style="text-align: center;">--}}
{{--    <thead>--}}
{{--    <tr>--}}
{{--        <td style="text-align: left; padding-left:35px;"><strong>PV No. </strong><span--}}
{{--                style="border-bottom:1px solid gray; width:20px;">{{ $paymentVoucher->uid }}</span></td>--}}
{{--        <th style="text-align: left; padding-right:-20px;"><strong>Date : </strong>{{ $paymentVoucher->date }}</th>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <th style="text-align: left; padding-left:35px;"><strong>Debit Account :</strong></th>--}}
{{--        <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->debitAccount->name }}</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <th style="text-align: left; padding-left:35px;"><strong>Payment Account :</strong></th>--}}
{{--        <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->cashBankAccount->name }}</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <th style="text-align: left; padding-left:35px;"><strong>Amount :</strong></th>--}}
{{--        <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->amount }}</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <th style="text-align: left; padding-left:35px;"><strong>Description :</strong></th>--}}
{{--        <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->narration }}</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <th style="text-align: left; padding-left:35px;"><strong>Referance :</strong></th>--}}
{{--        <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->reference_no }}</td>--}}
{{--    </tr>--}}

{{--    </thead>--}}
{{--</table>--}}
{{--<htmlpagefooter name="page-footer">--}}

{{--    <table width="100%">--}}
{{--        <tbody>--}}
{{--        <tr>--}}
{{--            <td style="text-align: left;"><span style="border-top: 1px solid hsl(0, 0%, 2%);">Showroom Incharge :</span>--}}
{{--            </td>--}}
{{--            <td style="text-align: right;"><span style="border-top: 1px solid hsl(0, 0%, 2%);">Accounts :</span></td>--}}
{{--        </tr>--}}
{{--        </tbody>--}}
{{--    </table>--}}
{{--    <hr>--}}

{{--    @php--}}
{{--        $date = new DateTime('now', new DateTimezone('Asia/Dhaka'));--}}
{{--    @endphp--}}
{{--    <br>--}}
{{--    <strong style="font-size: 8px">--}}
{{--        Printing Time:- {{ $date->format('F j, Y, g:i a') }}--}}
{{--    </strong>--}}
{{--    <br>--}}
{{--</htmlpagefooter>--}}
</body>

</html>
