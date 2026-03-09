<!DOCTYPE html>
<html>

<head>
    <title>Receipt Voucher </title>
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
<p style="text-align:center;  font-size: 20px;">Receipt Voucher</p>
<table>
    <tr>
        <td>No. : <span class="bold">{{ $voucher->uid }}</span></td>
        <td class="textRight">Dated : <span class="bold">{{ $voucher->date }}</span></td>
    </tr>
    <tr>
        <td colspan="2">On Account Of : {{ $voucher->narration }}</td>
    </tr>
</table>
<br><br>
<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
    <tr>
        <th class="bold border">#</th>
        <th class="bold border">Income Head</th>
        <th class="bold border">Receive Mode</th>
        <th class="bold border">Payee Name</th>
        <th class="bold border">Reference No</th>
        <th class="textRight bold border">Amount</th>
    </tr>
    </thead>
    <tbody>
    @php
        $totalAmount = 0;
    @endphp
    @foreach($receiveVouchers as $index => $item)
    @php
        $totalAmount += $item->amount;
    @endphp
    <tr>
        <td class="border">{{ $index + 1 }}</td>
        <td class="border">{{ $item->creditAccount->name ?? '' }}</td>
        <td class="border">{{ $item->debitAccount->name ?? '' }}</td>
        <td class="border">{{ $item->payee_name }}</td>
        <td class="border">{{ $item->reference_no }}</td>
        <td class="textRight border">{{ commaSeperated($item->amount) }}</td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5" class="textRight bold border">Total:</td>
        <td class="textRight bold border">{{ commaSeperated($totalAmount) }}</td>
    </tr>
    <tr>
        <td colspan="6" class="border">
            <span class="bold">Amount (in words):</span>
            Bangladeshi Taka {{ numberToWords($totalAmount) }} Only
        </td>
    </tr>
    </tfoot>
</table>
<br>
<br>
<br>
<br>
<table>
    <tr>
        <td></td>
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
</body>

</html>
