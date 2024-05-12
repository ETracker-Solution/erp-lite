<!DOCTYPE html>
<html>

<head>
    <title>Consumption </title>
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
                                    <strong> {{ Auth::guard('web')->user()->company->name ?? 'Company Name' }}</strong>
                                </h2>
                                <p style="text-align: center; padding: 0px; margin: 0px;"> {{ Auth::guard('web')->user()->company->address ?? 'Address' }}</p>
                            </div>
                            <div class="row">

                                <div class="col-sm-6 top-left">
                                    <i class="fa fa-rocket"></i>
                                </div>

                                <div class="col-sm-6 top-right">
                                    <h3 class="marginright">Consumption No-{{ $stock_adjust->consumption_no }}</h3>
                                    <span class="marginright">{{ \Carbon\Carbon::parse($stock_adjust->created_at)->isoFormat('MMM Do, YYYY') }}</span>
                                </div>

                            </div>
                            <hr>
                            <table border="1"cellspacing="0" width="100%" style="text-align: center;">
                                <tbody>
                                    <tr>
                                        <td style="text-align: left">
                                            <p class="lead margin bottom payment-info"><b> Consumption Details</b></p>
                                            <p><b>Date :</b> {{ $stock_adjust->created_at->format('Y-m-d') }}</p>
                                            <p><b>Status :</b> {{ $stock_adjust->status }}</p>
                                            <p><b>Grand Total :</b> BDT {{ $stock_adjust->grand_total }} </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table border="1"cellspacing="0" width="100%" style="text-align: center; margin-top:20px;">
                                <thead style="background:#cdced2;">
                                    <tr style="background-color: #cdced2;">
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Unit</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                @php
                                    $i=1;
                                @endphp
                                <tbody>
                                    @forelse ($stock_adjust->items as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $row->coi ? $row->coi->name : '' }}</td>
                                        <td>{{ $row->coi->parent->name }}</td>
                                        <td>{{ $row->coi->unit->name }}</td>
                                        <td>{{ $row->quantity }}</td>
                                    </tr>
                                    @empty

                                    @endforelse
                                    <tr>
                                        <td colspan="5">
                                            <b>Remark: </b>{{$stock_adjust->remark}}
                                        </td>
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
                                <br>
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left;">Customer Signature</td>
                                            <td style="text-align: right;">Saller Signature</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </htmlpagefooter>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>

