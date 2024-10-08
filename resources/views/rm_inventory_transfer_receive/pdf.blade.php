
<!DOCTYPE html>
<html>

<head>
    <title>RM Inventory Transfer Receive Pdf </title>
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
                            <div class="row">
                                <div class="col-sm-6 top-right">
                                    <span class="marginright">{{ \Carbon\Carbon::parse($rmTransferReceive->created_at)->isoFormat('MMM Do, YYYY') }}</span>
                                </div>
                            </div>
                            <hr>
                            <table width="100%">
								<tbody>
									<tr>
										<td style="text-align: left; padding:8px; line-height: 1.6">
											<p><b>RMITR No :</b> {{ $rmTransferReceive->uid }}</p>
											<p><b>Date :</b> {{ $rmTransferReceive->date }} </p>
											<p><b>Status :</b> {!! showStatus($rmTransferReceive->status) !!}</p>
										</td>
									</tr>
								</tbody>
							</table>
                            <table border="1"cellspacing="0" width="100%" style="text-align: center; margin-top:20px;">
                                <thead style="background:#cdced2;">
									<tr style="background-color: #cdced2;">
										<th>#</th>
                                        <th>Date</th>
                                        <th>From Store</th>
                                        <th>To Store</th>
                                        <th>Quantity</th>
                                        <th>Rate</th>
									</tr>
                                </thead>
                                <tbody>
                                    @foreach ($rmTransferReceive->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $rmTransferReceive->date }}</td>
                                            <td>{{ $rmTransferReceive->fromStore->name ?? '' }}</td>
                                            <td>{{ $rmTransferReceive->toStore->name ?? '' }}</td>
                                            <td>{{ $item->quantity ?? '' }}</td>
                                            <td>{{ $item->rate ?? '' }} TK</td>
                                        </tr>
                                        @endforeach
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

