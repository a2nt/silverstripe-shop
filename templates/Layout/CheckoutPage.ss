<% include Content %>
<div id="Checkout">
	<% if $Cart %>
		<% with $Cart %>
			<% include Cart %>
		<% end_with %>
		<% if $Cart.Items %>$OrderForm<% end_if %>
	<% else %>
		<div class="alert alert-error"><%t CheckoutPage.CARTEMPTY 'Your cart is empty.' %></p>
	<% end_if %>
</div>
