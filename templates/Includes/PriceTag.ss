<div class="pricetag">
	<% if DiscountedPrice %>
		<span class="original strikeout">
			<span class="symbol">$Price.Symbol</span>
			<strong class="main">$Price.Main</strong>
			<small class="fractional">$Price.Fractional</small>
			<span class="code">$Price.CurrencyCode</span>
		</span>
		<span class="discounted">$DiscountedPrice.Whole</span> Save: <span class="savings">$DiscountedPrice.Savings</span>
	<% else %>
		<span class="original"><strong class="price">$Price.Whole</strong></span>
	<% end_if %>
	<% if RecommendedPrice %><span>$RecommendedPrice.Whole</span><% end_if %>
</div>