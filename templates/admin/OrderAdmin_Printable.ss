<html>
	<head>
		<% base_tag %>
		$MetaTags
		<% include OrderReceiptStyle %>
	</head>
	<body>
		<div style="page-break-after: always;">
			<h1 class="title">$SiteConfig.Title <%t Cart.ORDER 'Order' %> #$Reference</h1>
			<% include Order %>
		</div>
	</body>
</html>

