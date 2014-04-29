<div class="hproduct h-product">
	<h1 class="title">
		<a href="$AbsoluteLink" class="fn url p-name u-url" rel="canonical">
			$Title
		</a>
	</h1>
	
	<div class="image">
		<% if $Image.ContentImage %>
			<img class="u-photo photo" src="$Image.ContentImage.URL" alt="<%t Product.IMAGE '{name} image' name=$Title %>" />
		<% else %>
			<div class="noimage"><%t Product.NOIMAGE 'no image' name=$Title %></div>
		<% end_if %>
	</div>
	
	<div class="details">
		<% if $InternalItemID %>
			<div class="var sku">
				<span class="title"><%t Product.CODE "Product Code" %>:</span>
				<span class="u-identifier identifier">
					<span class="type hidden">SKU</span>
					<span class="value">{$InternalItemID}</span>
				</span>
			</div>
		<% end_if %>
		
		<% if $Model %>
			<div class="var model">
				<span class="title"><%t Product.MODEL "Model" %>:</span>
				<span class="model">{$Model.XML}</span>
			</div>
		<% end_if %>
		
		<% if $Size %>
			<div class="var size">
				<span class="title"><%t Product.SIZE "Size" %>:</span>
				<span class="u-identifier">{$Size.XML}</span>
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
					<strong class="value">$Price.Whole</strong>
					<span class="currency">$Price.Currency</span>
				</div>
			<% end_if %>
		<% end_if %>

		<div class="category-links">
			<a href="$Link" class="category p-category">$Parent.Title</a>
			<% if $ProductCategories %>
				<% loop $ProductCategories %>
				, <a href="$Link" class="category p-category">$Title</a>
				<% end_loop %>
			<% end_if %>
		</div>

		<% if $Content %>
		<div class="e-description descriptio typography">
			$Content
		</div>
		<% end_if %>
	</div>
</div>