<div class="hproduct h-product">
	<% if $Image %>
	<a href="$Link" title="<%t Cart.READMORE 'Click here to read more on &quot;{name}&quot;' name=$Title %>">
		<img src="$Image.Thumbnail.URL" alt="<%t Product.IMAGE '{name} image' name=$Title %>" />
	</a>
	<% end_if %>
	
	<h3 class="title">
		<a 
			href="$Link"
			title="<%t Cart.READMORE 'Click here to read more on &quot;{name}&quot;' name=$Title %>"
			class="url fn photo p-name u-photo u-url"
		 >$Title</a>
	</h3>
	<% if $Model %>
	<div class="var model">
		<span class="title"><%t Product.MODEL "Model" %>:</span>
		<span class="model">{$Model.XML}</span>
	</div>
	<% end_if %>

	<% if $PriceRange %>
		<div class="price">
			<strong class="value">$PriceRange.Min.Nice</strong>
			<% if PriceRange.HasRange %>
				- <strong class="value">$PriceRange.Max.Nice</strong>
			<% end_if %>
			<span class="currency">$Price.Currency</span>
		</div>
	<% else %>
		<% if $Price %>
			<div class="p-price price">
				<strong class="value">$Price.Nice</strong>
				<span class="currency">$Price.Currency</span>
			</div>
		<% end_if %>
	<% end_if %>

	<% if $View %>
		<div class="view">
			<a href="$Link" title="<%t Produc.VIEWTITLE 'View &quot;{name}&quot;' name=$Title %>">
				<%t Produc.VIEWLINK 'View' %>
			</a>
		</div>
	<% else %>
		<% if $canPurchase %>
		<div class="add">
			<a href="$addLink" title="<%t Product.ADDONE 'Add &quot;{name}&quot; to Cart' name=$Title %>">
				<%t Product.ADDLINK 'Add to Cart' %>
			</a>
		</div>
		<% end_if %>
	<% end_if %>
	</div>
</div>																			
