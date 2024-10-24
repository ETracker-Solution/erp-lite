<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $model->invoice_number ?? 'Invoice' }} </title>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        table,
        td,
        th {
            border: 1px solid #08010C;
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
            background-color: red;
        }

        .fs {
            font-size: 12px;
        }
        .title {
            font-size: 20px;
        }

        @page {
            header: page-header;
            footer: page-footer;
        }

        .gtc {
            text-align: center;
            border-radius: 15px;
        }

        .sgtc {
            background-color: green;
            color: white;
            font-size: 20px;


        }

        .page-break {
            page-break-after: always;
        }
    </style>

</head>

<body>

    <htmlpageheader name="page-header">
        <table style="border: 2px solid #fff;">
            <tr>
                <td class="tc bb-none title">
                    E-Moto Bazar
                </td>
            </tr>
            <tr>
                <td class="tc" style="font-size: 10px;">
                    Address : 17/1, 60 Feet, Mirpur, Dhaka-1215
                </td>
            </tr>
        </table>

    </htmlpageheader>
    <br>
    <table style="border: 2px solid #fff;">
        <tr>
            <td class="tc bb-none">
               <p style="font-size: 10px;" class="tc">INVOICE NO# {{ $model->invoice_number }}</p>
            </td>
        </tr>
    </table>
    <table style="border: 2px solid #fff;">
        <tr>
            <td class="tc bb-none">
               <p style="font-size: 10px;">D&T: {{ $model->created_at }}</p>
            </td>
        </tr>
        <tr>
            <td class="tc" style="border-bottom: 1px solid #000">
              <p style="font-size: 10px;">Served By: {{ $model->user->name ?? '' }}</p>
            </td>
        </tr>
    </table>
<br>
    <table style="border: 2px solid #000;">
        <tr class="bb">
            <th class="tc bb">#</th>
            <th class="tc bb">Item Name</th>
            <th class="tc bb">Qty</th>
            <th class="tc bb">Price</th>
            <th class="tc bb">Item Total</th>
        </tr>
        @foreach ($model->items as $row)
        <tr>
            <td class="tc bb">{{$loop->iteration }}</td>
            <td class="tc bb">{{ $row->product->name ?? '' }}</td>
            <td class="tc bb">{{ $row->quantity ?? '' }} {{ $row->product->unit_of_measurement->name ?? '' }}</td>
            <td class="tc bb">{{ $row->sale_price ?? '' }} TK</td>
            <td class="tr bb">{{ $row->sale_price * $row->quantity ?? '' }} TK</td>
        </tr>
        @endforeach

    </table>
    <htmlpagefooter name="page-footer">
        <p style=" font-size: 14px;" class="tc">Thank You!</p>
    </htmlpagefooter>
</body>

</html>