<table class="table">
	<thead>
		<tr class="title">
			<th colspan="2">
				<h2><%t Cart.CUSTOMER 'Customer' %></h2>
			</th>
		</tr>
		<tr class="header">
			<th class="main"><%t Cart.CUSTOMERNAME 'Name' %></th>
			<th class="main"><%t Cart.CUSTOMEREMAIL 'Email' %></th>
		</tr>
	</thead>
	<tbody>
		<tr class="table-item">
			<td>$Name</td>
			<td>
				<% if LatestEmail %>
					<a href="mailto:$LatestEmail">$LatestEmail</a>
				<% end_if %>
			</td>
		</tr>
	</tbody>
</table>