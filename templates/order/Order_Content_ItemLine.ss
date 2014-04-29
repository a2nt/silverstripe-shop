<tr  class="itemRow $EvenOdd $FirstLast">
	<td>
		<% if $Image %>
			<div class="image">
				<a href="$Link" title="<% sprintf(_t("READMORE","View &quot;%s&quot;"),$Title) %>">
					<img src="$Image.setWidth(45).AbsoluteURL" alt="$Buyable.Title"/>
				</a>
			</div>
		<% end_if %>
	</td>
	<td class="product title" scope="row">
		<% if $Link %>
			<a href="$Link" title="<% sprintf(_t("READMORE","View &quot;%s&quot;"),$Title) %>">$TableTitle</a>
		<% else %>
			$TableTitle
		<% end_if %>
		<% if $SubTitle %><p class="subtitle">$SubTitle</p><% end_if %>
	</td>
	<td class="center unitprice">$UnitPrice.Whole</td>
	<td class="center quantity">$Quantity</td>
	<td class="right total">$Total.Whole</td>
</tr>