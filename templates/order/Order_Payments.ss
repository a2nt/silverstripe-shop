<table id="PaymentTable" class="infotable table table-striped table-bordered table-hover">
	<thead>			
		<tr class="gap mainHeader">
				<th colspan="10" class="left"><%t Cart.PAYMENTS 'Payment(s)' %></th>
		</tr>
		<tr>
			<th scope="row" class="twoColHeader"><%t Cart.DATE 'Date' %></th>
			<th scope="row"  class="twoColHeader"><%t Cart.AMOUNT 'Amount' %></th>
			<th scope="row"  class="twoColHeader"><%t Cart.PAYMENTSTATUS 'Payment Status' %></th>
			<th scope="row" class="twoColHeader"><%t Cart.PAYMENTMETHOD 'Method' %></th>
			<th scope="row" class="twoColHeader"><%t Cart.PAYMENTNOTE 'Note' %></th>
		</tr>
	</thead>
	<tbody>
		<% loop Payments %>	
			<tr>
				<td class="price">$Created.Nice</td>
				<td class="price">$Amount.Whole $Currency</td>
				<td class="price">$Status</td>
				<td class="price">$Gateway</td>
				<td class="price">$Message.NoHTML</td>
			</tr>
			<% if ShowMessages %>
				<% loop Messages %>
					<tr>
						<td colspan="5">
							$ClassName $Message $User.Name
						</td>
					</tr>
				<% end_loop %>
			<% end_if %>
		<% end_loop %>
	</tbody>
</table>