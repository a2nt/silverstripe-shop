<div id="MainContentContainer">
	<% include AccountPageMenu %>
	<div class="content">
		<% if $Order %>
			<% with $Order %>
				<h2><%t AccountPage.ORDERTITLE 'Order' %> #{$Reference} ({$Created.FormatI18N('%d %B %Y')})</h2>
			<% end_with %>
		<% end_if %>
		<% if $Message %>
			<div class="alert $MessageType">$Message</div>
		<% end_if %>
		<% if $Order %>
			<% with $Order %>
				<% include Order_Status %>		
				<% include Order %>
			<% end_with %>
			<div id="FormHolder">
				$Form
			</div>
		<% end_if %>
	</div>
	<div id="PageContainer">
		<div id="ContentContainer" class="typography">
			$Content
		</div>
	</div>
</div>