<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        h4 {
            margin: 0;
        }

        .w-full {
            width: 100%;
        }

        .w-half {
            width: 50%;
        }

        .margin-top {
            margin-top: 1.25rem;
        }

        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(255, 255, 255);
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table.products {
            font-size: 0.875rem;
            border: 1px solid black;
        }

        table.products tr {
            background-color: rgb(0, 43, 96);
            border: 1px solid black;
        }

        table.products th {
            color: #ffffff;
            padding: 0.5rem;
            border: 1px solid black;
        }

        table tr.items {
            background-color: rgb(243, 248, 253);
        }

        table tr.items td {
            padding: 0.5rem;
            border: 1px solid black;
        }

        .total {
            text-align: right;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        .headers{
            text-align: center
        }
    </style>
    <title>Inventory Report</title>
</head>
<body>
<table class="w-full">
    <tr>
        <td class="w-full headers">
            <h3>{{ $report_header }}</h3>
            <span>{{ $dateRange }}</span>
        </td>
    </tr>
</table>
<table  class="w-full">
    <tr>
        <td>{{ $page_title }}</td>
    </tr>
</table>
<div class="margin-top">
    <table class="products">
        <tr>
            @foreach($columns as $column)
                <th>{{$column}}</th>
            @endforeach
        </tr>
        @foreach($data as $key=>$item)
            <tr class="items">
                @foreach($columns as $column)
                    <td>{{ $item->$column }}</td>
        @endforeach

        @endforeach
    </table>
</div>
<htmlpagefooter name="page-footer">
    {PAGENO}
</htmlpagefooter>
</body>
</html>
