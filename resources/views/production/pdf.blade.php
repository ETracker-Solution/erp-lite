<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production PDF</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            text-align: center;
        }

        .header img {
            width: 100px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-info h2 {
            margin: 5px 0;
        }

        .company-info p {
            margin: 0;
        }

        .details, .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details th, .details td, .items th, .items td {
            padding: 10px;
            text-align: left;
            border: 1px solid #000;
        }

        .footer {
            margin-top: 30px;
        }

        .footer div {
            display: inline-block;
            width: 30%;
            text-align: center;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-section div {
            text-align: center;
            font-weight: bold;
            width: 32%;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .footer p {
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .details, .items th, .items td, .details th, .details td {
                font-size: 12px;
                padding: 8px;
            }

            .signature-section div {
                width: 100%;
                margin-bottom: 20px;
            }

            .signature-section {
                display: block;
                text-align: center;
            }

            .footer div {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .header img {
                width: 80px;
            }

            .company-info h2 {
                font-size: 16px;
            }

            .company-info p {
                font-size: 12px;
            }

            .details, .items {
                font-size: 10px;
            }

            .details th, .details td, .items th, .items td {
                padding: 5px;
            }
        }

    </style>
</head>
<body>

<div class="company-info">
    @include('common.pdf_header')
</div>

<table style="width: 100%;margin-top: 60%;">
    <tr>
        <td style="text-align: left">
            @if (auth()->check())
                <h3 class="marginright">User: {{ auth()->user()->name }}</h3>
            @endif
        </td>
        <td style="text-align: right">
            <h3 class="marginright">Production No: {{ $production->uid }}</h3>
        </td>
    </tr>
</table>

<hr>

<table class="details">
    <tr>
        <td style="text-align: left">
            <p class="lead marginbottom payment-info"><b> Production Details</b></p>
            <p><b>Date :</b> {{ $production->created_at->format('Y-m-d') }}</p>
            <p><b>Batch No : </b> {{ $production->batch ? $production->batch->batch_no : "Batch Not Found" }} </p>
            <p><b>Status :</b> {{ $production->status }}</p>
            <p><b>Reference :</b> {{ $production->reference_no }}</p>
        </td>
        <td style="text-align: right">
            <p><b>Description :</b> {{ $production->remark ?? "N/A" }}</p>
        </td>
    </tr>
</table>

<table class="items">
    <thead style="background:#cdced2;">
    <tr style="background-color: #cdced2;">
        <th>#</th>
        <th>Group</th>
        <th>Item</th>
        <th>Unit</th>
        <th>Unit Price</th>
        <th>Quantity</th>
        <th>Item Total</th>
    </tr>
    </thead>
    <tbody>
    @php
        $i=1;
    @endphp
    @foreach ($production->items as $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $row->coi->parent ? $row->coi->parent->name : '' }}</td>
            <td>{{ $row->coi ? $row->coi->name : '' }}</td>
            <td>{{ $row->coi->unit ? $row->coi->unit->name : '' }}</td>
            <td>{{ $row->rate }}</td>
            <td>{{ $row->quantity }}</td>
            <td>{{ $row->rate * $row->quantity }}</td>
        </tr>
    @endforeach
    </tbody>
</table>


<div class="signature-section" style="display: flex; justify-content: space-between; margin-top: 100px">
    <span style="border-top: 1px solid hsl(0, 0%, 2%);">Authorized By</span>&emsp;&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;&emsp;&emsp;&emsp;
    <span style="border-top: 1px solid hsl(0, 0%, 2%);">Supervisor</span>&emsp;&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;&emsp;&emsp;&emsp;
    <span style="border-top: 1px solid hsl(0, 0%, 2%);">Factory In-charge</span>
</div>

<hr>
@include('common.report_footer')
</body>
</html>




