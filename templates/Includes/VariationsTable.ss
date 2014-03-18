<table class="variationstable">
	<tr>
		<th><%t Product.VARIATION 'Variation' %></th>
		<th><%t Product.PRICE 'Price' %></th>
		<% if canPurchase %><th><%t Product.QUANTITYCART 'Quantity in cart' %></th><% end_if %>
	</tr>
	<% loop Variations %>
			<tr>
				<td>$Title.XML</td>
				<td>$Price.Nice $Currency</td>
				<td>
				<% if canPurchase %>
					<% if IsInCart %>
						<% with Item %>
							$QuantityField
						<% end_with %>
					<% else %>
						<a href="$Item.addLink" title="<%t Product.ADD 'Add &quot;{name}&quot; to your cart' name=$Title.XML %>">
							<%t Product.ADDLINK 'Add this item to cart' %>
						</a>
					<% end_if %>

				<% end_if %>
				</td>
			</tr>
	<% end_loop %>
</table>