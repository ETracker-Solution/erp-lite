<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ 'Print Invoice' }} </title>

    <style>
        @page {
            size: 72mm 250mm;
            margin: 0 !important;

        }

        .company_name {
            font-weight: 700;
            font-size: 24px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table,
        td,
        th {
            border: 1px solid #08010C;
        }

        .b-none {
            border: 2px solid transparent;
        }

        .bb-none {
            border-bottom: 2px solid transparent;
        }

        .br-none {
            border-right: 2px solid #fff;
        }

        .bt-none {
            border-top: 2px solid #fff;
        }

        .bl-none {
            border-left: 2px solid #fff;
        }

        .tc {
            text-align: center;
        }

        .tr {
            text-align: right;
        }

        body {
            font-family: bangla;
            font-size: 13px;
            /*background-color: red;*/
            margin: 0;
            padding: 0;
        }

        .fs {
            font-size: 12px;
        }

        @page {
            header: page-header;
            footer: page-footer;
        }

        .gtc {
            text-align: center;
            border-radius: 15px;
        }

        .text-bold {
            font-weight: 600;
        }

        .mm10 {
            margin-top: -10px;
        }
        table, tr, td {
            page-break-inside: avoid;
        }

    </style>

</head>

<body>

<htmlpageheader name="page-header">
    <table style="border: 2px solid #fff;">
        <tr>
            <td class="tc bb-none company_name">
                <img src="{{ public_path('upload'.'/'.getSettingValue('company_logo')) }}" alt=""
                     style="height: 50px;">
            </td>
        </tr>
{{--        <tr>--}}
{{--            <td class="tc bb-none" style="font-size: 10px;">--}}
{{--                {{ $sale->outlet->address }}--}}
{{--            </td>--}}
{{--        </tr>--}}
        <tr>
            <td class="bb-none tc"><p style="font-size: 10px;" class="tc">VAT Reg:001649431-0301</p></td>
        </tr>
        <tr>
            <td class="bb-none tc"><p style="font-size: 10px;" class="tc text-bold mm10">
                    Customer Care: 01638393939</p></td>
        </tr>
{{--        <tr>--}}
{{--            <td class="bb-none tc"><p style="font-size: 10px;" class="tc text-bold mm10"><u>{{ $sale->outlet->name }}--}}
{{--                        Invoice</u></p></td>--}}
{{--        </tr>--}}
    </table>

</htmlpageheader>
<hr>
<table style="border: 2px solid #fff;">
    <tr>
        <td class="b-none text-bold">Invoice #{{ $sale->invoice_number }}</td>
        <td class="b-none tr  text-bold">{{ getTimeByFormat($sale->created_at, 'd-M-Y') }}</td>
    </tr>
    <tr>
        <td class="b-none ">Customer Name:</td>
        <td class="b-none ">{{ $sale->customer->name }}</td>
    </tr>
    <tr>
        <td class="b-none ">Customer Address:</td>
        <td class="b-none ">{{ $sale->customer->address }}</td>
    </tr>
    <tr>
        <td class="b-none ">Served By:{{ $sale->created_by ? $sale->createdBy->name : 'N/A' }}</td>
        <td class="b-none tr text-bold">Time:{{getTimeByFormat($sale->created_at)}}</td>
    </tr>
</table>

<table style="border: 1px solid #000;">
    <tr>
        <td class="text-bold tc bl-none br-none">SL</td>
        <td class="text-bold tc bl-none br-none">Item Description</td>
        <td class="text-bold tc bl-none br-none">Unit Price (Tk.)</td>
        <td class="text-bold tc bl-none br-none">Qty</td>
        <td class="text-bold tc bl-none br-none">Total Price (Tk.)</td>
    </tr>
    @forelse($sale->items as $item)
        <tr>
            <td class="tc bl-none br-none">{{ $loop->iteration }}</td>
            <td class="tc bl-none br-none">{{ $item->coi->name }}</td>
            <td class="tc bl-none br-none">{{ $item->unit_price }}</td>
            <td class="tc bl-none br-none">{{ $item->quantity }}</td>
            <td class="tc bl-none br-none tr">{{ $item->unit_price * $item->quantity }}</td>
        </tr>
    @empty
        No Items
    @endforelse
    @php
        $total = $sale->items->sum(function ($q){
            return $q->unit_price * $q->quantity;
        });
        $vat = $sale->vat ?? 0;
        $sub_total = $total + $vat;
        $discount = $sale->discount ?? 0;
        $net_amount = $sub_total- $discount;
        $payment_point = $sale->payments()->where('payment_method','point')->first();
        $point_redeem = $payment_point ? $payment_point->amount : 0;
        $earned_point = 0;
        $current_point = 0;
        if (isset($sale->membershipPointHistory[0])){
        $earned_point = $sale->membershipPointHistory[0] && $sale->membershipPointHistory[0]->member_type_id != 1 ? $sale->membershipPointHistory()->where('point','>', 0)->first()->point : 0;
        $current_point = $sale->customer && $sale->customer->membership  && $sale->customer->membership->member_type_id == $sale->membershipPointHistory[0]->member_type_id && $sale->customer->id != 1 ? $sale->customer->currentReedemablePoint() : 0;
}
        $previous_point = $sale->customer->currentReedemablePoint() > 0 ? $sale->customer->currentReedemablePoint() - $earned_point : 0;


        $paid_amount = $sale->payments->sum('amount');
        $change_amount =  $paid_amount - $net_amount
    @endphp
    <tr>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none">{{ $sale->items->sum('quantity') }}</td>
        <td class="tc bl-none br-none tr">{{$total}}</td>
    </tr>
    <tr>
        <td class="tc bl-none br-none bt-none"></td>
        <td class="tc bl-none br-none bt-none"></td>
        <td class="tc bl-none br-none bt-none"></td>
        <td class="tc bl-none br-none bt-none">VAT:</td>
        <td class="tc bl-none br-none bt-none tr">{{ $vat }}</td>
    </tr>
    <tr>
        <td class="tc bl-none br-none bb-none"></td>
        <td class="tc bl-none br-none bb-none"></td>
        <td class="tc bl-none br-none bb-none"></td>
        <td class="tc bl-none br-none bb-none">Sub Total:</td>
        <td class="tc bl-none br-none bb-none tr">{{ $sub_total }}</td>
    </tr>
    <tr>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none">Discount:</td>
        <td class="tc bl-none br-none tr">{{ $discount }}</td>
    </tr>
    <tr>
        <td class="tc bl-none br-none bt-none"></td>
        <td class="tc bl-none br-none bt-none"></td>
        <td class="tc bl-none br-none bt-none" colspan="2">Net Amount:</td>
        <td class="tc bl-none br-none bt-none tr">{{ $net_amount }}</td>
    </tr>
    <tr>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none" colspan="2">Paid Amount</td>
        <td class="tc bl-none br-none tr">{{ $paid_amount }}</td>
    </tr>
    <tr>
        <td class="tc bl-none br-none bt-none"></td>
        <td class="tc bl-none br-none bt-none"></td>

        <td class="tc bl-none br-none bt-none" colspan="2">Change Amount:</td>
        <td class="tc bl-none br-none bt-none tr">{{ $change_amount }}</td>
    </tr>
</table>
<p class="text-bold">Payment Info:</p>
<table>
    <tr>
        <td class="tc bl-none br-none">Description</td>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none tr">Amount</td>
    </tr>
    @forelse($sale->payments as $payment)
        <tr>
            <td class="tc bl-none br-none">{{ ucwords($payment->payment_method) }}</td>
            <td class="tc bl-none br-none"></td>
            <td class="tc bl-none br-none"></td>
            <td class="tc bl-none br-none tr">{{ $payment->amount }}</td>
        </tr>
    @empty
        Not Found
    @endforelse

    <tr>
        <td class=" b-none"></td>
        <td class=" b-none"></td>
        <td class=" b-none"></td>
        <td class=" b-none"></td>
    </tr>
    <tr>
        <td class="tc b-none"></td>
        <td class="tc b-none"></td>
        <td class="tc b-none">Previous Point</td>
        <td class="b-none tr">{{ $previous_point }}</td>
    </tr>
    <tr>
        <td class="tc b-none"></td>
        <td class="tc b-none"></td>
        <td class="tc b-none">Redeem Point</td>
        <td class="b-none tr">{{ $point_redeem }}</td>
    </tr>
    <tr>
        <td class="b-none"></td>
        <td class="b-none"></td>
        <td class="tc b-none">Earned Point</td>
        <td class="b-none tr"> {{ $earned_point }}</td>
    </tr>
    <tr>
        <td class="b-none"></td>
        <td class="b-none"></td>
        <td class="tc b-none">Current Point</td>
        <td class="b-none tr">{{ $current_point  }}</td>
    </tr>

</table>
{{--@php--}}
{{--    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();--}}
{{--@endphp--}}
{{--<br><br>--}}
{{--<div style="text-align: center">--}}

{{--    <img  src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('000005263635', $generatorPNG::TYPE_CODE_128)) }}">--}}
{{--    <p style="padding: 0; margin: 0">000005263635</p>--}}
{{--</div>--}}

<htmlpagefooter name="page-footer">
    <hr>
    <h3 class="text-bold tc">{{ $sale->message }}</h3>
    <hr>
    <h4 class="tc"><em>
            System By:E-Tracker Software solution Ltd
        </em></h4>

</htmlpagefooter>
</body>

</html>
