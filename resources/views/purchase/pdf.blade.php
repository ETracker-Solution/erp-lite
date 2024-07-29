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
        body{margin-top:20px;
        background:#eee;
        }

        /*Invoice*/
        .invoice .top-left {
            font-size:65px;
            color:#3ba0ff;
        }

        .invoice .top-right {
            text-align:right;
            padding-right:20px;
        }
        @media(max-width:575px) {
            .invoice .top-left,.invoice .top-right,.invoice .payment-details {
                text-align:center;
            }

            .invoice .from,.invoice .to,.invoice .payment-details {
                float:none;
                width:100%;
                text-align:center;
                margin-bottom:25px;
            }

            .invoice p.lead,.invoice .from p.lead,.invoice .to p.lead,.invoice .payment-details p.lead {
                font-size:22px;
            }

            .invoice .btn {
                margin-top:10px;
            }
        }

        @media print {
            .invoice {
                width:900px;
                height:800px;
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
                                <h2 style="text-align:center; color: #4e73df; padding: 0px; margin: 0px; margin-left: 20px;" class="text-primary">
                                    <strong> {{ getSettingValue('company_name') }}</strong>
                                </h2>
                                <p style="text-align: center; padding: 0px; margin: 0px;">Address : {{ getSettingValue('company_address') }}</p>
                                <p style="text-align: center; padding: 0px; margin: 0px;">Email : {{ getSettingValue('company_email') }}</p>
                                <p style="text-align: center; padding: 0px; margin: 0px;">Phone : {{ getSettingValue('company_phone') }}</p>
                            </div>
                            <br>
                            <div class="col-sm-4 invoice-col">
                                <b>GPB No :</b> {{ $model->uid }},
                                <b>Name :</b> {{ $model->supplier->name }},
                                <b>Address :</b>  {{ $model->supplier->address }}.
                            </div>

                            <hr>
                            <table border="1"cellspacing="0" width="100%" style="text-align: center; margin-top:20px;">
                                <thead style="background:#cdced2;">
                                    <tr style="background-color: #cdced2;">
                                        <th>#</th>
                                        <th>Group</th>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th class="text-right">Item Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->coi->parent->name?? '' }}</td>
                                        <td>{{ $item->coi->name?? '' }}</td>
                                        <td>{{ $item->coi->unit->name?? '' }}</td>
                                        <td>{{ $item->quantity?? '' }} {{ $item->product->unit->name?? '' }}</td>
                                        <td>{{ $item->rate?? '' }}</td>
                                        <td class="text-right">{{ $item->rate * $item->quantity?? '' }}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-left">
                                            Sub Total
                                        </td>
                                        <td class="text-right">{{$model->subtotal}} </td>
                                    </tr>

                                </tbody>
                            </table>
                            <htmlpagefooter name="page-footer">
                                @php
                                    $date = new DateTime('now', new DateTimezone('Asia/Dhaka'));
                                @endphp
                                <br>
                                <strong>
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
