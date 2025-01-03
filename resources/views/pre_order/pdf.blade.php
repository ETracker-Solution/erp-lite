<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pre Order </title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }

        body {
            margin-top: 20px;
            background: #eee;
            font-family: 'notoserifbengali', sans-serif;
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

        @media (max-width: 575px) {
            .invoice .top-left, .invoice .top-right, .invoice .payment-details {
                text-align: center;
            }

            .invoice .from, .invoice .to, .invoice .payment-details {
                float: none;
                width: 100%;
                text-align: center;
                margin-bottom: 25px;
            }

            .invoice p.lead, .invoice .from p.lead, .invoice .to p.lead, .invoice .payment-details p.lead {
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
                <div class="panel-body">
                    <div class="invoice-ribbon">
                        @include('common.pdf_header')
                    </div>
                    <hr>
                    <b>Delivery Date :</b> {{ $model->delivery_date }},<br>
                    <b>Delivery Time :</b> {{ \Carbon\Carbon::parse($model->delivery_time)->format('h:i A') }},
                    <br>
                    <b>Order No :</b> {{ $model->order_number }}, <br>
                    <b>Customer :</b> {{ $model->customer->name ?? '' }}, <br>
                    <b>Customer Number
                        :</b> {{ $model->customer->type == 'default' ? 'N/A' : $model->customer->mobile }}, <br>
                    <b>From Outlet :</b> {{ $model->outlet ? $model->outlet->name : '' }}, <br>
                    <b>Delivery Point :</b> {{ $model->deliveryPoint ? $model->deliveryPoint->name : '' }}, <br>
                    <b>Size and Shape:</b> {{ $model->size }}, <br>
                    <b>Flavour :</b> {{ $model->flavour }}, <br>
                    <b>Cake Message :</b> {{ $model->cake_message }}, <br>
                    <b>Description :</b> {{ $model->remark }}. <br>

                    <table border="1" cellspacing="0" width="100%" style="text-align: center; margin-top:20px;">
                        <thead style="background:#cdced2;">
                        <tr style="background-color: #cdced2;">
                            <th>#</th>
                            <th>Group</th>
                            <th>Item</th>
                            <th>Rate</th>
                            <th>Qty</th>
                            <th>Discount</th>
                            <th class="text-right">Item Total</th>
                        </tr>
                        </thead>
                        @php
                            $i=1;
                        @endphp
                        <tbody>
                        @foreach ($model->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->coi->parent ? $item->coi->parent->name : '' }}</td>
                                <td>{{ $item->coi ? $item->coi->name : '' }}</td>
                                <td>{{ $item->unit_price }}</td>
                                <td>{{ $item->quantity?? '' }} {{ $item->product->unit->name?? '' }}</td>
                                <td>{{ $item->discount }}</td>
                                <td class="text-right">{{ $item->unit_price * $item->quantity }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="5"></td>
                            <td class="text-left">Sub Total:</td>
                            <td class="text-right">{{$model->subtotal}} </td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td class="text-left">Delivery Charge:</td>
                            <td class="text-right">{{$model->sale->delivery_charge ?? 0}} </td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td class="text-left">Additional Charge:</td>
                            <td class="text-right">{{$model->sale->additional_charge ?? 0}} </td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td class="text-left">Discount:</td>
                            <td class="text-right">{{ $model->discount }}</td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td class="text-left">Grand Total:</td>
                            <td class="text-right">{{ $model->grand_total }}</td>

                        </tr>

                        </tbody>
                    </table>
                    <table style="margin-top: 250px; width:100%; text-align: center !important;">
                        <tr style="text-align: center !important;">
                            <td><strong style="border-top: 1px solid black;text-align: end !important; ">Authorized
                                    By</strong></td>
                            <td><strong
                                    style="border-top: 1px solid black;text-align: center !important; ">Supervisor</strong>
                            </td>
                            <td><strong style="border-top: 1px solid black;text-align: start !important; ">Delivery
                                    Man</strong></td>
                        </tr>
                    </table>
                    @include('common.report_footer')
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>

