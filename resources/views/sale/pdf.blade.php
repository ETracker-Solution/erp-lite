<!DOCTYPE html>
<html>

<head>
    <title>Sale </title>
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
                                @include('common.pdf_header')
                            </div>
                            <hr>
                            <table width="100%">
								<tbody>
									<tr>
										<td style="text-align: left; padding:8px; line-height: 1.6">
                                            <p><b>Outlet :</b> {{ $sale->outlet->name }}</p>
                                            <p><b>Address :</b> {{ $sale->outlet->address }}</p>
											<p><b>Customer :</b> {{ $sale->customer->name }}</p>
                                            <p><b>Email :</b> {{ $sale->customer->email }} </p>
                                            <p><b>Phone :</b> {{ $sale->customer->mobile }} </p>
                                            <p><b>Address :</b> {{ $sale->customer->address }}</p>
{{--                                            @if(isset($sale->delivery_time))--}}
{{--                                                <p><b>Delivery Time :</b> {{ \Carbon\Carbon::parse($sale->delivery_time)->format('h:i A') }}</p>--}}
{{--                                            @else--}}

{{--                                            @endif--}}
										</td>
									</tr>
								</tbody>
							</table>
                            <table border="1"cellspacing="0" width="100%" style="text-align: center; margin-top:20px;">
                                <thead style="background:#cdced2;">
                                <tr style="background-color: #cdced2;">
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Item</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Discount</th>
                                    <th class="text-right">Grand Total</th>
                                </tr>
                                </thead>
                                @php
                                    $i=1;
                                @endphp
                                <tbody>
                                @foreach ($sale->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $item->coi->name ?? '' }}</td>
                                        <td>{{ $item->unit_price ?? 0 }}</td>
                                        <td>{{ $item->quantity ?? 0 }}</td>
                                        <td>{{ $item->discount ?? 0 }}</td>
                                        <td class="text-right">{{ ($item->unit_price * $item->quantity) - $item->discount }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td colspan="5"></td>
                                    <td class="text-left">Sub Total:</td>
                                    <td class="text-right">{{ $sale->subtotal }} </td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                    <td class="text-left">Taxable Amount :</td>
                                    <td class="text-right">{{ round($sale->taxable_amount, 0) }} </td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                    <td class="text-left">Vat Charge :</td>
                                    <td class="text-right">{{ $sale->vat }} </td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                    <td class="text-left">Additional Charge:</td>
                                    <td class="text-right">{{ $sale->additional_charge }} </td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                    <td class="text-left">Additional Charge:</td>
                                    <td class="text-right">{{ $sale->additional_charge }} </td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                    <td class="text-left">Discount:</td>
                                    <td class="text-right">{{ $sale->discount }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                    <td class="text-left">Grand Total:</td>
                                    <td class="text-right">{{ $sale->grand_total }}</td>

                                </tr>

                                </tbody>
                            </table>
                            <htmlpagefooter name="page-footer">
                                @php
                                    $date = new DateTime('now', new DateTimezone('Asia/Dhaka'));
                                @endphp
                                <br>
                                <strong style="font-size: 8px">
                                    Printing Time:- {{ $date->format('F j, Y, g:i a') }}
                                </strong>
                                <hr>
                                <br>
                                {{-- <table width="100%">
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

