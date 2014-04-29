<div class="parcel-rate parcel-rate-fedex">
	<% if $Main.Notifications %>
	<div class="alert alert-block">
		<% loop $Main.Notifications %>
			<div class="message">$LocalizedMessage</div>
		<% end_loop %>
	</div>
	<% end_if %>
	<% with $Main.RateReplyDetails %>
		Service Type: $ServiceType
		Transit Time: $TransitTime
	<% end_with %>
	Total Net Charge: $RateDetails.TotalNetCharge.Amount
</div>