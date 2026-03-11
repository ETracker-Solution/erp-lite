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
    <div class="invoice-ribbon">
        @include('common.pdf_header')
    </div>
    <p style="text-align:center;  font-size: 13px;">Receive Voucher</p>
    <hr>
    <table width="100%" style="text-align: center;">
        <thead>
            <tr>
                <td style="text-align: left; padding-left:35px;"><strong>CRV No. </strong><span style="border-bottom:1px solid gray; width:20px;">{{ $voucher->uid }}</span></td>
                <th style="text-align: left; padding-right:-20px;"><strong>Date : </strong>{{ $voucher->date }}</th>
            </tr>
            <tr>
                <th style="text-align: left; padding-left:35px;"><strong>Description :</strong></th>
                <td style="text-align: left; padding-right:-20px;">{{ $voucher->narration }}</td>
            </tr>
        </thead>
    </table>
    <br>
    <table width="100%" border="1" cellpadding="5" cellspacing="0" style="text-align: center; border-collapse: collapse;">
        <thead>
            <tr>
                <th>#</th>
                <th>Receive Mode</th>
                <th>Customer</th>
                <th>Invoice No</th>
                <th>Amount</th>
                <th>Settle Discount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
                $totalDiscount = 0;
            @endphp
            @foreach($customerReceiveVouchers as $index => $item)
            @php
                $totalAmount += $item->amount;
                $totalDiscount += $item->settle_discount;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->debitAccount->name ?? '' }}</td>
                <td>{{ $item->customer->name ?? '' }}</td>
                <td>{{ $item->sale ? $item->sale->invoice_number : 'N/A' }}</td>
                <td>{{ number_format($item->amount, 2) }}</td>
                <td>{{ number_format($item->settle_discount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">Total:</th>
                <th>{{ number_format($totalAmount, 2) }}</th>
                <th>{{ number_format($totalDiscount, 2) }}</th>
            </tr>
        </tfoot>
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
