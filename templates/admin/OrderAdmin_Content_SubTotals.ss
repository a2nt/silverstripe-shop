<tfoot>
	<tr class="table-item">
		<th colspan="4" class="main"><%t Cart.SUBTOTAL 'Sub-total' %></th>
		<th class="main">$SubTotal.Nice</th>
	</tr>
	<% loop Modifiers %>
		<% if ShowInTable %>
			<tr class="table-item table-item-$EvenOdd $FirstLast $Classes">
				<td colspan="4" class="main">$TableTitle</td>
				<td>$TableValue.Nice</td>
			</tr>
		<% end_if %>
	<% end_loop %>
	<tr class="table-item">
		<th colspan="4" class="main"><%t Cart.TOTAL 'Total' %></th>
		<th class="main">$Total.Nice $Currency</th>
	</tr>
</tfoot>
