<!DOCTYPE html>
<html>

<head>
    <title>Payment Datails </title>
    <style>
        table tr, th, td{
            padding-top:10px;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center; color: #4e73df; padding: 0px; margin: 0px; margin-left: 20px;" class="text-primary">
        <strong> {{ Auth::guard('web')->user()->business->name ?? 'Company Name' }}</strong>
    </h2>
    {{-- <p style="text-align:center;  font-size: 13px;"> Address : {{ Auth::guard('web')->user()->company->address ?? '' }} --}}
    </p>
    <p style="text-align:center;  font-size: 13px;">Payment Voucher</p>
    <hr>
    <table width="100%" style="text-align: center;">
        <thead>
            <tr>
                <td style="text-align: left; padding-left:35px;"><strong>PV No. </strong><span style="border-bottom:1px solid gray; width:20px;">{{ $paymentVoucher->pv_no }}</span></td>
                <th style="text-align: left; padding-right:-20px;"><strong>Date : </strong>{{ $paymentVoucher->date }}</th>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Debit Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->debitAccount->name }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Payment Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->cashBankAccount->name }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Amount :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->amount }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Description :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->narration }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Referance :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $paymentVoucher->reference_no }}</td>
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
