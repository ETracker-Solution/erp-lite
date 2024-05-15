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
        p {
            font-size: 12px;
        }

        .w-full {
            width: 100%;
        }
        .margin-top {
            margin-top: 1rem;
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
            font-size: 11px;
            /*border: 1px solid black;*/
        }

        table.products tr {
            background-color: #dfdfdf;
        }

        table.products th {
            padding: 0.5rem;
        }

        table tr.items {
            background-color: rgb(243, 248, 253);
        }

        table tr.items td {
            padding: 0.5rem;
            border-bottom: 1px solid #dfdfdf;
        }
        .headers {
            text-align: center
        }
    </style>
    <title>{{$report_header}}</title>
</head>
<body>
<table class="w-full">
    <tr>
        <td class="w-full headers">
            <h3>CAKE TOWN BD</h3>
            <p>
                157 Distrilary Road, Gandaria, Dhaka, Bangladesh
            </p>
        </td>
    </tr>
    <tr>
        <td class="w-full headers">
            <span style="font-size: 14px;font-weight: bold">{{ $report_header }}</span>
            <p>{{ $dateRange }}</p>
        </td>
    </tr>
</table>
<table class="w-full">
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
