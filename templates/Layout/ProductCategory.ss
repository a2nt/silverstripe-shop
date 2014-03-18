<% include Content %>

<div id="Sidebar" class="pull-right well">
	<% include ProductMenu %>
	<div class="cart">
		<% include SideCart %>
	</div>
</div>

<% if $Products %>
<div id="ProductGroup" class="row">
	<ul class="product-list unstyled">
		<% loop Products %>
		<div class="span3">
			<% include ProductGroupItem %>
		</div>
		<% end_loop %>
	</ul>
	<% with $Products %>
		<% include Pagination %>
	<% end_with %>
</div>
<% end_if %>
