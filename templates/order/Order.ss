<div id="OrderInformation">
	<% include Order_Address %>
	<% include Order_Parcels %>
	<% include Order_Content %>
	<% if $Total %>
		<% if $Payments %>
			<% include Order_Payments %>
		<% end_if %>
		<table id="OutstandingTable" class="infotable table table-bordered">
			<tbody>
				<tr class="gap summary" id="Outstanding">
					<th colspan="4" scope="row" class="threeColHeader"><strong><%t Cart.TOTALOUTSTANDING 'Total outstanding' %></strong></th>
					<td class="right"><strong>$TotalOutstanding.Whole </strong></td>
				</tr>
			</tbody>
		</table>
	<% end_if %>
	<% if $Notes %>
		<table id="NotesTable" class="infotable">
			<thead>
				<tr>
					<th><%t Cart.ORDERNOTES 'Notes' %></th>
				</tr>
			</thead>
			</tbody>
				<tr>
					<td>$Notes</td>
				</tr>
			</tbody>
		</table>
	<% end_if %>
</div>