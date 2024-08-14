
<!DOCTYPE html>
<html>

<head>
    <title>FG Requisiton Delivery Pdf </title>
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
                            <div class="row">
                                <div class="col-sm-6 top-right">
                                    <span class="marginright">{{ \Carbon\Carbon::parse($fgRequisitionDelivery->created_at)->isoFormat('MMM Do, YYYY') }}</span><br>
                                    <h3> Delivery Challan No : {{ $fgRequisitionDelivery->uid }}</h3>
                                </div>
                            </div>
                            <hr>
                            <table width="100%">
                                <tbody>
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
                                </tbody>
                            </table>
                            <table border="1"cellspacing="0" width="100%" style="text-align: center; margin-top:20px;">
                                <thead style="background:#cdced2;">
									<tr style="background-color: #cdced2;">
										<th>#</th>
										<th>Group</th>
										<th>Item</th>
										<th>Unit</th>
                                        <th>Requsition QTY</th>
                                        <th>Delivery QTY</th>
										<th>Remaining QTY</th>
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
                                            <td>{{ $requisition_qty- $delivery_qty  }}</td>
                                            {{-- <td>{{ $item->rate ?? '' }} TK</td> --}}
                                        </tr>
                                       
                                    @endforeach
                                </tbody>
                            </table>
                            <htmlpagefooter name="page-footer">
                                
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left;"><span style="border-top: 1px solid hsl(0, 0%, 2%);">Factroy Supervisor</span> </td>
                                            <td style="text-align: center;"><span style="border-top: 1px solid hsl(0, 0%, 2%);">Delivery Man</span></td>
                                            <td style="text-align: right;"><span style="border-top: 1px solid hsl(0, 0%, 2%);">Showroom Incharge</span> </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>

                                @php
                                    $date = new DateTime('now', new DateTimezone('Asia/Dhaka'));
                                @endphp
                                <br>
                                <strong>
                                    Printing Time:- {{ $date->format('F j, Y, g:i a') }}
                                </strong>
                                <br>
                            </htmlpagefooter>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>

