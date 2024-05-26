<!DOCTYPE html>
<html>
<head>
	<title>{{ $model->invoice_number ?? 'Invoice' }} </title>
</head>
<body>
<h2 style="text-align:center; color: #4e73df; padding: 0px; margin: 0px; margin-left: 20px;" class="text-primary">
	<strong> Company Name</strong>
</h2>
<p style="text-align:center;  font-size: 13px;"> Address : 17/1, 60 Feet, Mirpur, Dhaka-1215</p>
<table width="100%" class="table" style="text-align: center;">
    <tbody class="text-center">
			<tr>
			   <td>
				 <h4 class="text-dark"><strong>Invoice No:- {{ $model->invoice_number }}</strong></h4>
			    </td>
			    <td>
				 <h5 class="text-dark">
				  <strong>Shop Owner Mobile:- 01710355789</strong></h5>
			    </td>
			    <td>
				<h5 class="text-dark">
					<strong>Shop Mobile:- 01871848137</strong>
				</h5>
			    </td>
		</tr>
	</tbody>
</table>
<hr>
<table width="100%">
	<tbody>
		<tr>
			<td width="100%" style="text-align: center; color: black;  padding: 10px 0px; font-size: 20px;"><h4><strong>Customer Information:-</strong></h4></td>
		</tr>
	</tbody>
</table>
<table width="100%">
	<tbody>
	  <tr>
	  <td>
		<h5 class="text-dark">
		 <strong>Name:- {{ $invoice->payment->customer->name ?? '' }} </strong>
		</h5>
	  </td>
		<td><h5 class="text-dark">
		 <strong>Mobile:- {{ $invoice->payment->customer->mobile?? ''  }}</strong>
		</h5>
	    </td>
		<td><h5 class="text-dark">
		 <strong>Email:- {{ $invoice->payment->customer->email?? ''  }}</strong>
		</h5>
	    </td>
		<td><h5 class="text-dark">
		 <strong>Address:- {{ $invoice->payment->customer->address ?? '' }}</strong>
		</h5>
	    </td>
	  </tr>
	</tbody>
</table>
<hr>
<table width="100%" style="text-align: center;">
	<thead style="background:#cdced2;">
        <tr>
           <th>SL NO.</th>
           <th>Category</th>
           <th>Product Name</th>
           <th>Quantity</th>
           <th>Unit Price</th>
           <th>Total Price</th>
        </tr>
    </thead>
    <tbody>
    	@php
    	$subTotal = 0;
    	@endphp
		@foreach ($item_details as $key => $invoiceDetal)

      <tr>
    	<td>{{ $key+1 }}</td>
    	<td>{{ $invoiceDetal->category_name ??'' }}</td>
    	<td>{{ $invoiceDetal->name ??''}}</td>
    	<td>{{ $invoiceDetal->item_quantity }}</td>
    	<td>{{ $invoiceDetal->sale_price }}</td>
    	<td style="text-align: right;">{{ $invoiceDetal->item_total ?? '' }}</td>
      </tr>
      @php
      $subTotal += $invoiceDetal->item_total;
      @endphp
    @endforeach
    <tr>
    	<td colspan="5" style="text-align: right;">Sub Total:-</td>
    	<td style="text-align: right;">{{ $subTotal }}</td>
    </tr>
    <tr>
    	<td colspan="5" style="text-align: right;">Discount:-</td>
    	<td style="text-align: right;">{{ $model->discount ??''}}</td>
    </tr>
    <tr>
    	<td colspan="5" style="text-align: right;">Grant Total:-</td>
    	<td style="text-align: right;">{{ $model->grandtotal ??''}}</td>
    </tr>
    <tr>
    	<td colspan="5" style="text-align: right;">Receive :-</td>
    	<td style="text-align: right;">{{ $model->receive_amount ??''}}</td>
    </tr>
    <tr>
    	<td colspan="5" style="text-align: right;">Change :-</td>
    	<td style="text-align: right;">{{ $model->change_amount ??''}}</td>
    </tr>
    </tbody>
</table>
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
</body>
</html>
