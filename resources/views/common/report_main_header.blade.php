<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }

        h4 { margin: 0; }

        p { font-size: 12px; }

        .w-full { width: 100%; }

        .margin-top { margin-top: 1rem; }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table.products { font-size: 11px; }

        table.products tr th {
            background-color: #dfdfdf;
            padding: 0.5rem;
            text-align: left;
        }

        table tr.items td {
            padding: 0.5rem;
            border-bottom: 1px solid #dfdfdf;
            font-size: 11px;
        }

        .headers { text-align: center; }
    </style>
    <title>{{ $report_header }}</title>
</head>
<body>

<htmlpagefooter name="page-footer">
    <table width="100%">
        <tbody>
        <tr>
            <td>
                <p style="font-size: 8px; margin: 0;"><em>Printing on: {{ \Carbon\Carbon::now()->format('m-d-Y h:i a') }}</em></p>
            </td>
            <td style="text-align:right">
                <p style="font-size: 8px; text-align:right; margin: 0;"><em>Page {PAGENO} out of {nbpg}</em></p>
            </td>
        </tr>
        </tbody>
    </table>
</htmlpagefooter>

<table class="w-full">
    <tr>
        <td class="w-full headers" style="border: unset !important;">
            <h1>Welkin Pastry Ltd.</h1>
            <p>1182/A Nurani Para, East Monipur,<br>Mirpur-2, Dhaka</p>
            <p>Email: welkinpastry@gmail.com</p>
        </td>
    </tr>
    <tr>
        <td class="w-full headers">
            <span style="font-size: 14px; font-weight: bold;">{{ $report_header }}</span>
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
                <th>{{ $column }}</th>
            @endforeach
        </tr>
