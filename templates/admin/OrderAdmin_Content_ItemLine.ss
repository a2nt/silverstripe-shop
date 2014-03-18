<tr  class="table-item table-item-{$EvenOdd} $FirstLast">
	<td>
		<% if $Image %>
			<div class="image">
				<a href="$Link" title="<%t Cart.READMORE 'View &quot;{name}&quot;' name=$Title %>">
					<img src="<% with $Image.setWidth(45) %>$Me.AbsoluteURL<% end_with %>" alt="$Buyable.Title"/>
				</a>
			</div>
		<% end_if %>
	</td>
	<td class="product title">
		<strong>
		<% if $Link %>
			<a href="$Link" target="new">$TableTitle</a>
		<% else %>
			$TableTitle
		<% end_if %>
		</strong>
		<% if $SubTitle %><div class="subtitle">$SubTitle</div><% end_if %>
		<% if $Buyable.InternalItemID %><div class="sku"><%t Cart.SKU 'SKU' %>: $Buyable.InternalItemID</div><% end_if %>
	</td>
	<td class="unitprice">$UnitPrice.Nice</td>
	<td class="quantity">$Quantity</td>
	<td class="total">$Total.Nice</td>
</tr>