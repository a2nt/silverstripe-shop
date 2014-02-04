<div id="MainContentContainer">
	<% include AccountPageMenu %>
	<div class="span5 typography">
		<div class="h2"><%t AccountPage.DEFAULTADDRESS 'Default Addresses' %></div>
		<% if DefaultAddressForm %>
			$DefaultAddressForm
		<% else %>
			<div class="alert"><%t AccountPage.NOADDRESS 'No addresses found.' %></div>
		<% end_if %>
	</div>
	<div class="span4 typography">
		<div class="h2"><%t AccountPage.CREATEADDRESS 'Create New Address' %></div>
		<div class="well">
			$CreateAddressForm
		</div>
	</div>
</div>