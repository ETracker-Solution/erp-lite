<!DOCTYPE html>
<html>

<head>
    <title>Fund Transfer Voucher </title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }
        body{margin-top:20px;
        background:#eee;
        }

        /*Invoice*/
        .invoice .top-left {
            font-size:65px;
            color:#3ba0ff;
        }

        .invoice .top-right {
            text-align:right;
            padding-right:20px;
        }
        /* table tr, th, td{
            padding-top:10px;
        } */
        @media(max-width:575px) {
            .invoice .top-left,.invoice .top-right,.invoice .payment-details {
                text-align:center;
            }

            .invoice .from,.invoice .to,.invoice .payment-details {
                float:none;
                width:100%;
                text-align:center;
                margin-bottom:25px;
            }

            .invoice p.lead,.invoice .from p.lead,.invoice .to p.lead,.invoice .payment-details p.lead {
                font-size:22px;
            }

            .invoice .btn {
                margin-top:10px;
            }
        }

        @media print {
            .invoice {
                width:900px;
                height:800px;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-ribbon">
        @include('common.pdf_header')
    </div>
    <p style="text-align:center;  font-size: 20px;">Fund Transfer Voucher</p>
    <hr>
    <table width="100%" style="text-align: center;">
        <thead>
            <tr>
                <td style="text-align: left; padding-left:35px;"><strong>FTV No . </strong><span style="border-bottom:1px solid gray; width:20px;">{{ $fundTransferVoucher->id }}</span></td>
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
    <htmlpagefooter name="page-footer">
                                
        <table width="100%">
            <tbody>
                <tr>
                    <td style="text-align: left;"><span style="border-top: 1px solid hsl(0, 0%, 2%);">Showroom Incharge :</span> </td>
                    <td style="text-align: right;"><span style="border-top: 1px solid hsl(0, 0%, 2%);">Accounts :</span> </td>
                </tr>
            </tbody>
        </table>
        <hr>

        @php
            $date = new DateTime('now', new DateTimezone('Asia/Dhaka'));
        @endphp
        <br>
        <strong>
            Printing Time:- {{ $date->format('F j, Y, g:i a') }}
        </strong>
        <br>
    </htmlpagefooter>
</body>

</html>
