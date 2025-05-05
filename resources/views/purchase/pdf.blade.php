<!DOCTYPE html>
<html>

<head>
    <title>Purchase </title>
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

        @media(max-width:575px) {

            .invoice .top-left,
            .invoice .top-right,
            .invoice .payment-details {
                text-align: center;
            }

            .invoice .from,
            .invoice .to,
            .invoice .payment-details {
                float: none;
                width: 100%;
                text-align: center;
                margin-bottom: 25px;
            }

            .invoice p.lead,
            .invoice .from p.lead,
            .invoice .to p.lead,
            .invoice .payment-details p.lead {
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
        <div class="container bootstrap snippets bootdeys">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default invoice" id="invoice">
                        <div style="display: flex; justify-content: space-between;">
                            <div style="width: 50%; float: left;">
                                <p>PR No : <b>{{ $model->uid }}</b></p>
                                <p>Ref. No. : <b>{{ $model->reference_no ?? '' }}</b></p>
                            </div>
                            <div style="text-align: right; width: 50%; float: right;">
                                <p>Dated : <b>{{ $model->date }}</b></p>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div style="text-align: center;">
                                <p>Supplier Name: {{ $model->supplier->name }}</p>
                            </div>
                        </div>
                        <div class="panel-body">

                            <div class="invoice-ribbon">
                                @include('common.pdf_header')
                            </div>
                            <h4>Store: {{ $model->store->name }}</h4>
                            <hr>
                            <table
                                style="border: 1px solid black; border-collapse: collapse; width: 100%;  ; text-align: center; margin-top: 20px;">
                                <thead>
                                    <tr style="background-color: #cdced2;">
                                        <th style="border: 1px solid black; padding: 8px;width: 10%">SL no.</th>
                                        <th style="border: 1px solid black; padding: 8px;width: 10%;">Group</th>
                                        <th style="border: 1px solid black; padding: 8px;width: 10%;">Item</th>
                                        <th style="border: 1px solid black; padding: 8px;width: 10%;">Unit</th>
                                        <th style="border: 1px solid black; padding: 8px;width: 10%;">Unit Per Alt Unit</th>
                                        <th style="border: 1px solid black; padding: 8px;width: 10%;">Alt Unit</th>
                                        <th style="border: 1px solid black; padding: 8px;width: 10%;">Alt Qty</th>
                                        <th style="border: 1px solid black; padding: 8px;width: 10%;">Unit Qty</th>
                                        <th style="border: 1px solid black; padding: 8px;width: 10%;">Rate</th>
                                        <th style="border: 1px solid black; padding: 8px;width: 10%;">Value</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($model->items as $item)
                                                <tr>
                                                    <td style="border: 1px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: left;">
                                                        {{ $item->coi->parent->name ?? '' }}
                                                    </td>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: left;">
                                                        {{ $item->coi->name ?? '' }}
                                                    </td>
                                                    <td style="border: 1px solid black; padding: 8px;">{{ $item->coi->unit->name ?? '' }}</td>
                                                    <td style="border: 1px solid black; padding: 8px;">{{ $item->a_unit_quantity ?? '' }}</td>
                                                    <td style="border: 1px solid black; padding: 8px;">{{ $item->coi->alterUnit ? $item->coi->alterUnit->name : '' }}</td>
                                                    <td style="border: 1px solid black; padding: 8px;">{{ $item->unit_qty > 0 ? $item->unit_qty : '' }}</td>
                                                    <td style="border: 1px solid black; padding: 8px;">{{ $item->converted_unit_qty ?? '' }}</td>
                                                    <td style="border: 1px solid black; padding: 8px;">{{ $item->alt_unit_rate ?? '' }}</td>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: right; font-weight: bold;">
                                                        {{ number_format($item->rate * $item->quantity, 2) ?? '' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                    <!-- Total Row -->
                                    <tr>
                                        <td style="border: 1px solid black;" colspan="9">Total</td>
                                        <td style="border: 1px solid black; padding: 8px; text-align: right; font-weight: bold;">
                                            TK
                                            {{ number_format($model->subtotal, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="display: flex; justify-content: space-between;">
                                <div style="width: 70%; float: left;">
                                    <p>Amount Chargeable (in words)</p>
                                    <p style="font-weight: bold;">Bangladeshi Taka {{ numberToWords($model->subtotal) }}</p>
                                </div>
                                <div style="text-align: right; width: 30%; float: right;">
                                    <p style="font-style: italic;">E. & O.E</p>
                                </div>
                            </div>

                            <!-- Additional Details -->
                            <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                                <div>
                                    <p>Company’s Sales Tax No. : </p>
                                    <p>Buyer’s Sales Tax No. : </p>
                                    <p>Company’s CST No. : </p>
                                </div>
                                <div style="text-align: right;">
                                    <p style="font-weight: bold;">for {{ $model->supplier->name }}</p>
                                    <p>Authorised Signatory</p>
                                </div>
                            </div>
                            <htmlpagefooter name="page-footer">
                                @php
$date = new DateTime('now', new DateTimezone('Asia/Dhaka'));
                                @endphp
                                <br>
                                <strong style="font-size: 10px">
                                    Printing Time:- {{ $date->format('F j, Y, g:i a') }}
                                </strong>
                                <hr>
                                {{-- <br>
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left;">Customer Signature</td>
                                            <td style="text-align: right;">Saller Signature</td>
                                        </tr>
                                    </tbody>
                                </table> --}}
                            </htmlpagefooter>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>





    {{-- <table style="border: 2px solid #000;">
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
            <td class="tc bb">{{ $row->coi->name ?? '' }}</td>
            <td class="tc bb">{{ $row->quantity ?? '' }} {{ $row->coi->unit->name ?? '' }}</td>
            <td class="tc bb">{{ $row->rate ?? '' }} TK</td>
            <td class="tr bb">{{ $row->rate * $row->quantity ?? '' }} TK</td>
        </tr>
        @endforeach

    </table> --}}
