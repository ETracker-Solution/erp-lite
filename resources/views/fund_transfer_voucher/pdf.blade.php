<!DOCTYPE html>
<html>
<head>
    <title>Fund Transfer Voucher</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }

        body {
            margin-top: 20px;
        }

        table tr, th, td {
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="invoice-ribbon">
        @include('common.pdf_header')
    </div>
    <p style="text-align:center; font-size: 20px;">Fund Transfer Voucher</p>
    <hr>
    <table width="100%" style="text-align: center;">
        <thead>
            <tr>
                <td style="text-align: left; padding-left:35px;">
                    <strong>FTV No . </strong>
                    <span style="border-bottom:1px solid gray; width:20px;">{{ $fundTransferVoucher->uid ?: $fundTransferVoucher->id }}</span>
                </td>
                <td style="text-align: left; padding-right:-20px;">
                    <strong>Date : </strong>{{ $fundTransferVoucher->date }}
                </td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Transfer From Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $fundTransferVoucher->creditAccount->name ?? '—' }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Transfer To Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $fundTransferVoucher->debitAccount->name ?? '—' }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Amount :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ number_format((float) $fundTransferVoucher->amount, 2) }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Description :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $fundTransferVoucher->narration }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Reference :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $fundTransferVoucher->reference_no }}</td>
            </tr>
        </thead>
    </table>
    <htmlpagefooter name="page-footer">
        <table width="100%">
            <tbody>
                <tr>
                    <td style="text-align: left;"><span style="border-top: 1px solid hsl(0, 0%, 2%);">Showroom Incharge :</span></td>
                    <td style="text-align: right;"><span style="border-top: 1px solid hsl(0, 0%, 2%);">Accounts :</span></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <br>
        <strong>
            Printing Time:- {{ now('Asia/Dhaka')->format('F j, Y, g:i a') }}
        </strong>
        <br>
    </htmlpagefooter>
</body>
</html>
