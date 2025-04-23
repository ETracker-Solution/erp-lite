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

        body {
            margin-top: 20px;
            background: #eee;
        }

        /*Invoice*/
        .invoice .top-left {
            font-size: 65px;
            color: #3ba0ff;
        }

        .invoice .top-right {
            text-align: right;
            padding-right: 20px;
        }

        /* table tr, th, td{
            padding-top:10px;
        } */
        @media (max-width: 575px) {
            .invoice .top-left, .invoice .top-right, .invoice .payment-details {
                text-align: center;
            }

            .invoice .from, .invoice .to, .invoice .payment-details {
                float: none;
                width: 100%;
                text-align: center;
                margin-bottom: 25px;
            }

            .invoice p.lead, .invoice .from p.lead, .invoice .to p.lead, .invoice .payment-details p.lead {
                font-size: 22px;
            }

            .invoice .btn {
                margin-top: 10px;
            }
        }

        @media print {
            .invoice {
                width: 900px;
                height: 800px;
            }
        }
    </style>
</head>

<body>
<div class="invoice-ribbon">
    {{--            @include('common.pdf_header')--}}
</div>
<p style="text-align:center;  font-size: 20px;">Fund Transfer Receive </p>
<table style="margin-top: 70px">
    <tr>
        <td style="
        border-top: 1px solid black;
        "><strong>Authorized By</strong></td>
    </tr>
</table>
<hr>
<table width="100%" style="text-align: center;">
    <thead>
    <tr>
        {{--        <th>Outlet</th>--}}
        <th>Date</th>
        <th>From Account</th>
        <th>To Account</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @php
        $oid = null;
        $o_name = '';
        $subtotal = 0;
    @endphp

    @foreach($transactions as $key=>$transaction)
        @if(isset($transaction->creditAccount->outlets[0]))
            @if ($oid != null && ($oid != $transaction->creditAccount->outlets[0]->id))
                <tr>
                    <td colspan="3"><strong>Subtotal for {{ $o_name }}:</strong></td>
                    <td><strong>{{ number_format($subtotal, 2) }}</strong></td>
                </tr>

                @php
                    $subtotal = 0;
                @endphp
            @endif
            <tr>
                <td>{{ $transaction->date }}</td>
                <td>{{ $transaction->creditAccount->name }}</td>
                <td>{{ $transaction->debitAccount->name }}</td>
                <td>{{ number_format($transaction->amount,2) }}</td>
            </tr>

            @php
                $subtotal += $transaction->amount;
                $oid = $transaction->creditAccount->outlets[0]->id;
                $o_name = $transaction->creditAccount->outlets[0]->name;
            @endphp
        @else
            @if ($oid != null && ($oid != 'ho'))
                <tr>
                    <td colspan="3"><strong>Subtotal for {{ $o_name }}:</strong></td>
                    <td><strong>{{ number_format($subtotal, 2) }}</strong></td>
                </tr>

                @php
                    $subtotal = 0;
                @endphp
            @endif
            <tr>
                <td>{{ $transaction->date }}</td>
                <td>{{ $transaction->creditAccount->name }}</td>
                <td>{{ $transaction->debitAccount->name }}</td>
                <td>{{ number_format($transaction->amount,2) }}</td>
            </tr>

            @php
                $subtotal += $transaction->amount;
                $oid = 'ho';
                $o_name = 'HO';
            @endphp
        @endif

    @endforeach

    @if ($oid != null)
        <tr>
            <td colspan="3"><strong>Subtotal for {{ $o_name }}:</strong></td>
            <td><strong>{{ number_format($subtotal, 2) }}</strong></td>
        </tr>
    @endif
    <hr>
    <tr>
        {{--        <td></td>--}}
        <td></td>
        <td></td>
        <td><strong>Total</strong></td>
        <td><b>{{ number_format($totalAmount,2) }}</b></td>
    </tr>
    </tbody>
</table>
@include('common.report_footer')
</body>

</html>
