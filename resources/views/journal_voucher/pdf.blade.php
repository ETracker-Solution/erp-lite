<!DOCTYPE html>
<html>

<head>
    <title>Journal Voucher </title>
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
    <p style="text-align:center;  font-size: 13px;">Journal Voucher</p>
    <hr>
    <table width="100%" style="text-align: center;">
        <thead>
            <tr>
                <td style="text-align: left; padding-left:35px;"><strong>JV No. </strong><span style="border-bottom:1px solid gray; width:20px;">{{ $journalVoucher->jv_no }}</span></td>
                <td style="text-align: left; padding-right:-20px;"><strong>Date : </strong>{{ $journalVoucher->date }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Debit Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $journalVoucher->debitAccount->name }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Credit Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $journalVoucher->creditAccount->name }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Amount :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $journalVoucher->amount }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Description :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $journalVoucher->narration }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Referance :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $journalVoucher->reference_no }}</td>
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
