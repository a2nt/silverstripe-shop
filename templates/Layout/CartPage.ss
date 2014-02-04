<% include Content %>

<% if $Cart %>
	<% if $CartForm %>
		$CartForm
	<% else %>
		<% with $Cart %>
			<% include Cart Editable=true %>
		<% end_with %>
	<% end_if %>
<% else %>
	<div class="alert alert-error"><%t CartPage.CARTEMPTY 'Your cart is empty.' %></div>
<% end_if %>

<div class="cart-footer">
	<div class="btn-group">
	<% if $ContinueLink %>
		<a class="continuelink btn $BtnClass" href="$ContinueLink" rel="nofollow,noindex">
			<span class="icon icon-chevron-left"></span>
			<%t CartPage.CONTINUE 'Continue Shopping' %>
		</a>
	<% end_if %>
	<% if $Cart %>
		<% if $Cart.CheckoutLink %>
			<a class="checkoutlink btn btn-success" href="$Cart.CheckoutLink" rel="nofollow,noindex">
				<%t CartPage.PROCEEDTOCHECKOUT 'Proceed to Checkout' %>
				<span class="icon icon-chevron-right icon-white"></span>
			</a>
		<% end_if %>
	<% end_if %>
	</div>
</div>