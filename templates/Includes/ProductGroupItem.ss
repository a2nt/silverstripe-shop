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
	<div class="var identifier model">
		<span class="title"><%t Product.MODEL "Model" %>:</span>
		<span class="hidden type">model</span>
		<span class="value">{$Model.XML}</span>
	</div>
	<% end_if %>
	
	<% if $InternalItemID %>
	<div class="var identifier u-identifier">
		<span class="title">Serial Number:</span>
		<span class="hidden type">sku</span>
		#<span class="value">{$InternalItemID}</span>
	</div>
	<% end_if %>

	<% if $PriceRange %>
		<div class="price">
			<strong class="value">$PriceRange.Min.Nice</strong>
			<% if $PriceRange.HasRange %>
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

	<div class="controls">
		<% if $View %>
		<div class="view">
			<a href="$Link" title="<%t Produc.VIEWTITLE 'View &quot;{name}&quot;' name=$Title %>" class="url u-url">
				<%t Produc.VIEWLINK 'View' %>
			</a>
		</div>
		<% end_if %>
		<% if $canPurchase %>
		<a href="$addLink" title="<%t Product.ADDONE 'Add &quot;{name}&quot; to Cart' name=$Title %>" class="add" rel="nofollow,noindex">
			<%t Product.ADDLINK 'Add to Cart' %>
		</a>
		<% end_if %>
	</div>
</div>																		
