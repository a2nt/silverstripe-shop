<% if $Parcels && $Status == "Sent" %>
<table id="OrderParcels" class="infotable table table-bordered">
	<thead>
		<tr class="gap mainHeader">
			<th colspan="4">Parcels</th>
		</tr>
		<tr>
			<th>Delivery Service</th>
			<th>Tracking Number</th>
			<th>Size</th>
			<th>Delivery Status</th>
		</tr>
	</thead>
	<tbody>
	<% loop $Parcels %>
		<tr>
			<td>$HumanServiceName</td>
			<td>$TrackingNumber</td>
			<td>$Size</td>
			<td>$Status</td>
		</tr>
		<tr>
			<td colspan="4">$DeliveryDetails</td>
		</tr>
	<% end_loop %>
	</tbody>
</table>
<% end_if %>