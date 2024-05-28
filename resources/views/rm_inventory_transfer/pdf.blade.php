<!DOCTYPE html>
<html>

<head>
	<title>RM Inventory Pdf</title>
</head>

<body>
	<table width="100%">
		<tbody>
			<tr>
				<td width="100%" style="text-align: center; color: black;  padding: 10px 0px; font-size: 20px;">
					<h4><strong>RM Inventory Information:-</strong></h4>
				</td>
			</tr>
		</tbody>
	</table>
	<table width="100%">
		<tbody>
			<tr>
				<td style="text-align: left; padding:8px; line-height: 1.6">
					<p><b>UID :</b> {{ $RMInventoryTransfer->uid }}</p>
					<p><b>Date :</b> {{ $RMInventoryTransfer->date }} </p>
					<p><b>Transfer From :</b> {{ $RMInventoryTransfer->fromStore->name }} </p>
					<p><b>Transfer To :</b> {{ $RMInventoryTransfer->toStore->name }} </p>
					<p><b>Status :</b> {!! showStatus($RMInventoryTransfer->status) !!}</p>
				</td>
			</tr>
		</tbody>
	</table>
	<hr>
	<table width="100%" style="text-align: center;">
		<thead style="background:#cdced2;">
			<tr>
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
				<td style="text-align: left;">Signature</td>
				<td style="text-align: right;">Signature</td>
			</tr>
		</tbody>
	</table>
</body>

</html>