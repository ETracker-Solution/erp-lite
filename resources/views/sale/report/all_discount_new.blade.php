<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $report_header }}</title>

</head>

<body>
<div class="sub-title">{{ $dateRange }}</div>

<table>
    <thead>
    <tr>
        <th style="width: 12%">Date</th>
        <th style="width: 12%">Invoice</th>
        <th style="width: 26%">Customer</th>
        <th style="width: 16%">Mobile</th>
        <th style="width: 10%">Discount</th>
    </tr>
    </thead>

    <tbody>
    @php $total = 0; @endphp

    @foreach($data as $row)
        @php
            $discount = $row->sale_discount;
            $total += $discount;
        @endphp
        <tr>
            <td>{{ $row->date }}</td>
            <td>{{ $row->invoice_number }}</td>
            <td>{{ $row->customer_name }}</td>
            <td>{{ $row->customer_mobile }}</td>
            <td>{{ $discount }}</td>
        </tr>
    @endforeach

    <tr class="total-row">
        <td colspan="4" style="text-align: right;">Total</td>
        <td>{{ $total }}</td>
    </tr>
    </tbody>
</table>

</body>
</html>
