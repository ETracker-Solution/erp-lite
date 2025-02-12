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
        <td>No. : <span class="bold">{{ $receiveVoucher->uid }}</span></td>
        <td class="textRight">Dated : <span class="bold">{{ $receiveVoucher->date }}</span></td>
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
    <tr>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receiveVoucher->creditAccount->name }}</td>
        <td class="textRight border-left">{{commaSeperated($receiveVoucher->amount)}}</td>
    </tr>
    <tr>
        <td class="spacer"></td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td class="bold">Through:</td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receiveVoucher->debitAccount->name }}</td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td class="bold">On Account Of:</td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receiveVoucher->narration }}.</td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td class="bold">Amount (in words):</td>
        <td class="border-left">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bangladeshi Taka {{ numberToWords($receiveVoucher->amount) }} Only</td>
        <td class="textRight border-left border-top border-bottom-thick">TK {{ commaSeperated($receiveVoucher->amount) }}</td>
    </tr>
    </tbody>
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
