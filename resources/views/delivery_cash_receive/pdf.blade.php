<!DOCTYPE html>
<html>

<head>
    <title>Fund Transfer Voucher </title>
    <style>
        table tr, th, td{
            padding-top:10px;
        }
    </style>
</head>

<body>
    <div class="invoice-ribbon">
        @include('common.pdf_header')
    </div>
    <p style="text-align:center;  font-size: 13px;">Fund Transfer Voucher</p>
    <hr>
    <table width="100%" style="text-align: center;">
        <thead>
            <tr>
                <td style="text-align: left; padding-left:35px;"><strong>FTV No . </strong><span style="border-bottom:1px solid gray; width:20px;">{{ $fundTransferVoucher->ftv_no }}</span></td>
                <td style="text-align: left; padding-right:-20px;"><strong>Date : </strong>{{ $fundTransferVoucher->date }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Transfer From Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $fundTransferVoucher->debitAccount->name }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Transfer To Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $fundTransferVoucher->creditAccount->name }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Amount :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $fundTransferVoucher->amount }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Description :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $fundTransferVoucher->narration }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Referance :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $fundTransferVoucher->reference_no }}</td>
            </tr>      
        </thead> 
    </table>
    <table width="100%" style="margin: 100px;">
        <tbody>
            <tr>
                <td style="text-align: left"><p style="border-top: 1px solid #666;">&nbsp;Prepared By :</p></td>
                <td style="text-align: right"><p style=" border-top: 1px solid #666;">&nbsp;Received By :</p></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
