<div id="Sidebar" class="pull-right well">
	<% with Parent %>
		<% include ProductMenu %>
	<% end_with %>
	<div class="cart">
		<% include SideCart %>
	</div>
</div>

<div class="product">
	<% include HProduct %> 
</div>

<div id="FormContainer">
	$Form
</div>