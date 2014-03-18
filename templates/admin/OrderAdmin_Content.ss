<table class="table">
	<thead>
		<tr class="title">
			<th colspan="5">
				<h2><%t Cart.ITEMS 'Items' %></h2>
			</th>
		</tr>
		<tr class="header">
			<th class="main"></th>
			<th class="main"><span class="ui-button-text"><%t Cart.PRODUCT 'Product' %></span></th>
			<th class="main"><span class="ui-button-text"><%t Cart.UNITPRICE 'Unit Price' %></span></th>
			<th class="main"><span class="ui-button-text"><%t Cart.QUANTITY 'Quantity' %></span></th>
			<th class="main"><span class="ui-button-text"><%t Cart.TOTALPRICE 'Total Price' %> ($Currency)</span></th>
		</tr>
	</thead>
	<tbody>
		<% loop Items %>
			<% include OrderAdmin_Content_ItemLine %>
		<% end_loop %>
	</tbody>
	<% include OrderAdmin_Content_SubTotals %>
</table>