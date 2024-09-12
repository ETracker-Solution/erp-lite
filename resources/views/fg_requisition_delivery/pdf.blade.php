<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Delivery Challan</title>
    <style>
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

    {{-- <div class="header">
        <img src="logo.png" alt="Logo">
    </div> --}}

    <div class="company-info">
        @include('common.pdf_header')
    </div>

    <table class="details">
        <tr>
            <td style="text-align: left; padding:8px; line-height: 1.6">
                <p><b>FGR No :</b> {{ $fgRequisitionDelivery->requisition ? $fgRequisitionDelivery->requisition->uid : 'Not Available' }}</p>
                <p><b>Date :</b> {{ $fgRequisitionDelivery->date }} </p>
                <p><b>Status :</b> {!! showStatus($fgRequisitionDelivery->status) !!}</p>
                <p><b>Outlet :</b> {{ $fgRequisitionDelivery->requisition->outlet ? $fgRequisitionDelivery->requisition->outlet->name : '' }}</p>
                <p><b>Store :</b> {{ $fgRequisitionDelivery->toStore->name ?? ''}}</p>
                <p><b>Address :</b> {{ $fgRequisitionDelivery->requisition->outlet ? $fgRequisitionDelivery->requisition->outlet->address : '' }}</p>
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
                <th style="white-space:nowrap">Req. QTY</th>
                <th style="white-space:nowrap">Del. QTY</th>
                <th style="white-space:nowrap">Rem. QTY</th>
                {{-- <th>Rate</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($fgRequisitionDelivery->items as $item)
            @php
                $requisition_qty = getRequisitionQty($item->requisition_id, $item->coi_id);
                $delivery_qty = $item->quantity;
            @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->coi->parent->name ?? '' }}</td>
                    <td>{{ $item->coi->name ?? '' }}</td>
                    <td>{{ $item->coi->unit->name ?? '' }}</td>
                    <td>{{ $requisition_qty }}</td>
                    <td>{{ $delivery_qty }}</td>
                    <td>{{ max(($requisition_qty - $delivery_qty),0)  }}</td>
                    {{-- <td>{{ $item->rate ?? '' }} TK</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section" style="display: flex; justify-content: space-between; margin-top: 100px">
        <span style="border-top: 1px solid hsl(0, 0%, 2%);">Factory Supervisor</span>&emsp;&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;&emsp;&emsp;&emsp;
        <span style="border-top: 1px solid hsl(0, 0%, 2%);">Delivery Man</span>&emsp;&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;&emsp;&emsp;&emsp;
        <span style="border-top: 1px solid hsl(0, 0%, 2%);">Showroom Incharge</span>
    </div>
    <hr>
    <div class="footer">
        @php
            $date = new DateTime('now', new DateTimezone('Asia/Dhaka'));
        @endphp
        <br>
        <strong>
            Printing Time:- {{ $date->format('F j, Y, g:i a') }}
        </strong>
    </div>

</body>
</html>




