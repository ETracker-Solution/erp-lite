
<!DOCTYPE html>
<html>

<head>
    <title>FG Inventory Transfer Pdf </title>
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
											<p><b>User :</b> {{ auth()->user()->name }} -- ({{ auth()->user()->email }})</p>
											<p><b>FGIT No :</b> {{ $FGInventoryTransfer->uid }}</p>
											<p><b>Date :</b> {{ $FGInventoryTransfer->date }} </p>
											<p><b>Transfer From :</b> {{ $FGInventoryTransfer->fromStore->name }} </p>
											<p><b>Transfer To :</b> {{ $FGInventoryTransfer->toStore->name }} </p>
											<p><b>Status :</b> {!! showStatus($FGInventoryTransfer->status) !!}</p>
                                            <p><b>Creator :</b> {{ $FGInventoryTransfer->createdBy->name }} -- ({{ $FGInventoryTransfer->createdBy->email }})</p>
                                            <p><b>Reference Number :</b> {{ $FGInventoryTransfer->reference_no ?? '' }}</p>
                                            <p><b>Remarks :</b> {{ $FGInventoryTransfer->remarks ?? '' }}</p>
                                        </td>
									</tr>
								</tbody>
							</table>
                            <table border="1"cellspacing="0" width="100%" style="text-align: center; margin-top:20px;">
                                <thead style="background:#cdced2;">
									<tr style="background-color: #cdced2;">
										<th>#</th>
										<th>Group</th>
										<th>Name</th>
										<th>Unit</th>
										<th>Price</th>
										<th>Quantity</th>
									</tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
										<tr>
											<td>{{ $loop->iteration }}</td>
											<td>{{ $item->coi->parent ? $item->coi->parent->name : '' }}</td>
											<td>{{ $item->coi ? $item->coi->name : '' }}</td>
											<td>{{ $item->coi->unit->name }}</td>
											<td>{{ $item->rate ?? '' }}</td>
											<td>{{ $item->quantity ?? '' }}</td>
										</tr>
									@endforeach
                                </tbody>
                            </table>
                            <table style="margin-top: 100px; width:100%; text-align: center !important;">
                                <tr style="text-align: center !important;">
                                    <td><strong style="border-top: 1px solid black;text-align: end !important; ">Authorized
                                            By</strong></td>
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

