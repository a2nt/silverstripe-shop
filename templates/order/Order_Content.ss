<table id="InformationTable" class="infotable ordercontent">
	<colgroup class="image"/>
	<colgroup class="product title"/>
	<colgroup class="unitprice" />
	<colgroup class="quantity" />
	<colgroup class="total"/>
	<thead>
		<tr>
			<th scope="col"></th>
			<th scope="col"><%t Cart.PRODUCT 'Product' %></th>
			<th scope="col"><%t Cart.UNITPRICE 'Unit Price' %></th>
			<th scope="col"><%t Cart.QUANTITY 'Quantity' %></th>
			<th scope="col"><%t Cart.TOTALPRICE 'Total Price' %> ($Currency)</th>
		</tr>
	</thead>
	<tbody>
		<% loop $Items %>
			<% include Order_Content_ItemLine %>
		<% end_loop %>
	</tbody>
	<% include Order_Content_SubTotals %>
</table>