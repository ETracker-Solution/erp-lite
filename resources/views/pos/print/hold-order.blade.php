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
<h3 class="text-bold" style="margin-bottom: 0 !important;">UNPAID</h3>
<table style="border: 2px solid #fff;">
    <tr>
        <td class="b-none text-bold">Identifier # {{ $identifier }}</td>
        <td class="b-none tr  text-bold">{{ date('d-M-Y') }}</td>
    </tr>
{{--    <tr>--}}
{{--        <td class="b-none ">Customer Name:</td>--}}
{{--        <td class="b-none ">{{  }}</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <td class="b-none ">Customer Address:</td>--}}
{{--        <td class="b-none ">{{  }}</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <td class="b-none ">Served By:{{  }}</td>--}}
{{--        <td class="b-none tr text-bold">Time:{{}}</td>--}}
{{--    </tr>--}}
</table>

<table style="border: 1px solid #000;">
    <tr>
        <td class="text-bold tc bl-none br-none">SL</td>
        <td class="text-bold tc bl-none br-none">Item Description</td>
        <td class="text-bold tc bl-none br-none">Unit Price (Tk.)</td>
        <td class="text-bold tc bl-none br-none">Qty</td>
        <td class="text-bold tc bl-none br-none">Total Price (Tk.)</td>
    </tr>
    @foreach($items as $item)
        <tr>
            <td class="tc bl-none br-none">{{ $loop->iteration }}</td>
            <td class="tc bl-none br-none">{{ $item['name'] }}</td>
            <td class="tc bl-none br-none">{{ $item['price'] }}</td>
            <td class="tc bl-none br-none">{{ $item['quantity'] }}</td>
            <td class="tc bl-none br-none tr">{{ $item['total'] }}</td>
        </tr>
    @endforeach
    @php
        $total = collect($items)->sum(function ($q){
            return $q['price'] * $q['quantity'];
        });
        $vat =  0;
        $sub_total = $total + $vat;
        $discount = $sale->discount ?? 0;
        $net_amount = $sub_total- $discount;
    @endphp
    <tr>
        <td class="tc bl-none br-none bb-none"></td>
        <td class="tc bl-none br-none bb-none"></td>
        <td class="tc bl-none br-none bb-none" colspan="2">Sub Total:</td>
        <td class="tc bl-none br-none bb-none tr">{{ $sub_total }}</td>
    </tr>
    <tr>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none"></td>
        <td class="tc bl-none br-none"  colspan="2">Discount:</td>
        <td class="tc bl-none br-none tr">{{ $discount }}</td>
    </tr>
</table>

<htmlpagefooter name="page-footer">
    <hr>
{{--    <h3 class="text-bold tc">{{ $sale->message }}</h3>--}}
    <hr>
    <h4 class="tc"><em>
            System By:E-Tracker Solution
        </em></h4>

</htmlpagefooter>
</body>

</html>
