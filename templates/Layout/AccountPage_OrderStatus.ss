<div id="MainContentContainer">
	<% include AccountPageMenu %>
	<% if PastOrders %>
	<table class="infotable table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th><%t AccountPage.ORDERNUM 'Order No.' %></th>
				<th><%t Order.DATE 'Date' %></th>
				<th><%t CartPage.TOTAL 'Total Outstanding' %></th>
			</tr>
		</thead>
		<tbody>
		<% loop PastOrders %>
		<tr>
			<td><a href="/OrderStatus/order/{$ID}/" rel="nofollow">$Reference</a></td>
			<td>{$Created.Nice}</td>
			<td>$TotalOutstanding.Nice</td>
		</tr>
		<% end_loop %>
		</tbody>
	</table>
	<% else %>
		<div class="alert alert-info">
			<%t AccountPage.NoOrders "We haven't found any orders" %>
		</div>
	<% end_if %>
	<div id="PageContainer">
		<div id="ContentContainer" class="typography">
			$Content
		</div>
	</div>
</div>