<div class="cart">
<% if $Items %>
	<table class="items table table-striped table-hover table-condensed" summary="<%t Cart.TABLESUMMARY 'Current contents of your cart.' %>">
		<colgroup class="image"/>
		<colgroup class="product title"/>
		<colgroup class="unitprice" />
		<colgroup class="quantity" />
		<colgroup class="total"/>
		<colgroup class="remove"/>
		<thead>
			<tr>
				<th scope="col"></th>
				<th scope="col"><%t Cart.PRODUCT 'Product' %></th>
				<th scope="col"><%t Cart.UNITPRICE 'Unit Price' %></th>
				<th scope="col"><%t Cart.QUANTITY 'Quantity' %></th>
				<th scope="col"><%t Cart.TOTALPRICE 'Total Price' %> ($Currency)</th>
				<% if $Editable %>
					<th scope="col"><%t Cart.REMOVE 'Remove' %></th>
				<% end_if %>
			</tr>
		</thead>
		<tbody>
			<% loop $Items %><% if $ShowInTable %>
				<tr id="$TableID" class="$Classes $EvenOdd $FirstLast">
					<td class="image">
						<% if $Image %>
						<a href="$Link" title="<%t Cart.READMORE 'View &quot;{name}&quot;' name=$Buyable.Title %>">
							$Image.setWidth(45)
						</a>
						<% end_if %>
					</td>
					<td id="$TableTitleID">
						<% if $Link %>
							<a href="$Link" title="<%t Cart.READMORE 'View &quot;{name}&quot;' name=$Title %>">$TableTitle</a>
						<% else %>
							$TableTitle
						<% end_if %>
						<% if $SubTitle %>
							<div class="subtitle">$SubTitle</div>
						<% end_if %>
						<% if $VariationField && $Editable %>
							<div class="variation-field"><%--t Cart.VARIATION 'Change:' --%>$VariationField</div>
						<% end_if %>
					</td>
					<td>$UnitPrice.Whole</td>
					<td><% if $Editable %>$QuantityField<% else %>$Quantity<% end_if %></td>
					<td id="$TableTotalID">$Total.Whole</td>
					<% if $Editable %>
					<td>
						<% if $RemoveField %>
							$RemoveField
						<% else %>
							<a class="remove" href="$removeallLink" title="<%t Cart.REMOVEALL 'Remove {name} from cart' name=$Title %>">
								<span class="icon icon-remove"></span>
							</a>
						<% end_if %>
					</td>
					<% end_if %>
				</tr>			
			<% end_if %><% end_loop %>
		</tbody>
		<tfoot>
			<tr class="subtotal">
				<th colspan="4" scope="row"><%t Cart.SUBTOTAL 'Sub-total' %></th>
				<td id="$TableSubTotalID">$SubTotal.Whole</td>
				<% if $Editable %><td>&nbsp;</td><% end_if %>
			</tr>
			<% if $Modifiers %>
				<% loop $Modifiers %>
					<% if $ShowInTable %>
						<tr id="$TableID" class="$Classes">
							<th id="$TableTitleID" colspan="4" scope="row">
								<% if $Link %>
									<a href="$Link" title="<%t Cart.READMORE 'Click here to read more on &quot;{name}&quot;' name=$TableTitle %>">$TableTitle</a>
								<% else %>
									$TableTitle
								<% end_if %>
							</th>
							<td id="$TableTotalID">$TableValue.Whole</td>
							<td>
							<% if $CanRemove %>
								<a class="remove" href="$removeLink" title="<%t Cart.REMOVEALL 'Remove {name} from cart' name=$Title %>">
									<span class="icon icon-remove"></span>
								</a>
							<% end_if %>
							</td>
						</tr>
						<% if $Form %>
							<tr>
								<td colspan="5">$Form</td><td colspan="10"></td>
							</tr>
						<% end_if %>
					<% end_if %>
				<% end_loop %>
			<% end_if %>
			<tr class="gap Total">
				<th colspan="4" scope="row"><%t Cart.TOTAL 'Total' %></th>
				<td id="$TableTotalID"><span class="value">$Total.Whole</span> <span class="currency">$Currency</span></td>
				<% if $Editable %><td>&nbsp;</td><% end_if %>
			</tr>
		</tfoot>
	</table>
<% else %>
	<div class="no-items alert alert-error"><%t Cart.NOITEMS "There are no items in your cart" %>.</div>
<% end_if %>
</div>