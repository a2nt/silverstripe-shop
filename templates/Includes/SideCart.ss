<div class="cart cart-side">
	<div class="title"><%t Cart.HEADLINE "My Cart" %></div>
	<% if $Cart %>
		<% with $Cart %>
			<div class="count"><%t Cart.ITEMSTOTAL 'There are <a href="{url}" rel="nofollow,noindex">{count}</a> items in your cart' count=$Items.Quantity url=$CartLink %></div>
			<table class="items table table-striped table-hover table-condensed">
				<tbody>
				<% loop $Items %>
					<tr class="item">
						<td class="title">
							<a href="$Product.Link" title="<%t Cart.READMORE 'View &quot;{name}&quot;' name=$Title %>">
								$TableTitle
							</a>
							<% if $SubTitle %><div class="subtitle">$SubTitle</div><% end_if %>
						</td>
						<td class="quantity-price">
							<span class="quantity">$Quantity</span> <span class="times">x</span> <span class="unitprice">$UnitPrice.Nice</span>
						</td>
						<td>
							<a class="remove" href="$removeallLink" title="<%t Cart.REMOVEALL 'Remove {name} from cart' name=$TableTitle %>">
								<span class="icon icon-remove"></span>
							</a>
						</td>
					</tr>
				<% end_loop %>
				</tbody>
			</table>
			<a href="$CheckoutLink" title="<%t Cart.CheckoutClick %>" rel="nofollow,noindex" class="checkout">
				<%t Cart.CheckoutGoTo "Checkout" %>
			</a>
		<% end_with %>
	<% else %>
		<div class="no-items alert alert-error"><%t Cart.NOITEMS "There are no items in your cart" %>.</div>
	<% end_if %>
</div>