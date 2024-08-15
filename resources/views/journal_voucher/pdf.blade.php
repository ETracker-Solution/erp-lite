<!DOCTYPE html>
<html>

<head>
    <title>Journal Voucher </title>
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
        <h2 style="text-align:center; color: #4e73df; padding: 0px; margin: 0px; margin-left: 20px;" class="text-primary">
            <strong> {{ getSettingValue('company_name') }}</strong>
        </h2>
        <p style="text-align: center; padding: 0px; margin: 0px;">Address : {{ getSettingValue('company_address') }}</p>
        <p style="text-align: center; padding: 0px; margin: 0px;">Email : {{ getSettingValue('company_email') }}</p>
        <p style="text-align: center; padding: 0px; margin: 0px;">Phone : {{ getSettingValue('company_phone') }}</p>
    </div>
    <p style="text-align:center;  font-size: 20px;">Journal Voucher</p>
    <hr>
    <table width="100%" style="text-align: center;">
        <thead>
            <tr>
                <td style="text-align: left; padding-left:35px;"><strong>JV No. </strong><span style="border-bottom:1px solid gray; width:20px;">{{ $journalVoucher->uid }}</span></td>
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
