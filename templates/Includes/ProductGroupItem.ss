<div class="productItem">
	<% if Image %>
		<a href="$Link" title="<% sprintf(_t("READMORE","Click here to read more on &quot;%s&quot;"),$Title) %>">
			<img src="$Image.Thumbnail.URL" alt="<% sprintf(_t("IMAGE","%s image"),$Title) %>" />
		</a>
	<% else %>
		<a href="$Link" title="<% sprintf(_t("READMORE"),$Title) %>" class="noimage"><!-- no image --></a>
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
			<a href="$addLink" title="<%t Product.ADD 'Add &quot;{name}&quot; to Cart' name=$Title %>">
				<%t Product.ADDLINK 'Add to Cart' %>
			</a>
		</div>
		<% end_if %>
	</div>
</div>																			
