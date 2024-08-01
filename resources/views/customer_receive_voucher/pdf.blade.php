<!DOCTYPE html>
<html>

<head>
    <title>Receive Datails </title>
    <style>
        /* @page {
            header: page-header;
            footer: page-footer;
        } */
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
    <p style="text-align:center;  font-size: 13px;">Receive Voucher</p>
    <hr>
    <table width="100%" style="text-align: center;">
        <thead>
            <tr>
                <td style="text-align: left; padding-left:35px;"><strong>RV No. </strong><span style="border-bottom:1px solid gray; width:20px;">{{ $receiveVoucher->rv_no }}</span></td>
                <th style="text-align: left; padding-right:-20px;"><strong>Date : </strong>{{ $receiveVoucher->date }}</th>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Credit Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $receiveVoucher->creditAccount->name }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Debit Account  :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $receiveVoucher->debitAccount->name }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Amount :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $receiveVoucher->amount }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Description :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $receiveVoucher->narration }}</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Referance :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $receiveVoucher->reference_no }}</td>
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
    {{-- <htmlpagefooter name="page-footer">
        <table width="100%">
            <tbody>
                <tr>
                    <td style="padding-left:80px;"><p style="text-align:left; border-top: 1px solid #666;">&nbsp;Prepared By :</p></td>
                    <td style="padding-left:300px;"><p style="text-align:right;  border-top: 1px solid #666;">&nbsp;Received By :</p></td>
                </tr>
            </tbody>
        </table>
        @php
            $date = new DateTime('now', new DateTimezone('Asia/Dhaka'));
        @endphp
        <br>
        <p style="text-align:center"><span style="font-size:10px;">
            Printing Time:- {{ $date->format('F j, Y, g:i a') }}
        </span></p>
    </htmlpagefooter> --}}
</body>

</html>
