<% if $Tracks %>
<div class="parcel-tracks parcel-tracks-fedex">
	<% loop $Tracks %>
		<% if $Location %>
		<div class="track">
			<div class="company">
				<strong>$OperatingCompanyOrCarrierDescription</strong>
			</div>
			
			<% if $Packaging %>
			<div class="packaging">
				<span class="title">Packing</span>:
				<span class="value">$Packaging</span>
			</div>
			<% end_if %>

			<div class="time">
				<span class="title">Date</span>:
				<span class="value">
					<% if $ActualDeliveryTimestamp %>
						$ActualDeliveryTimestamp.ZendDate
					<% else %>
						$ShipTimestamp.ZendDate
					<% end_if %>
				</span>
			</div>
			<div class="location">
				<span class="title">Location</span>:
				<span class="value">{$Location}</span>
			</div>

			<% if $Events %>
			<div class="events">
				<% loop $Events %>
					<div class="event">
						<div class="description">
							<strong>$EventDescription</strong>
						</div>
						<div class="time">$Timestamp.ZendDate</div>
						<div class="location">$Location</div>
					</div>
				<% end_loop %>
			</div>
			<% end_if %>

			<% if $DeliverySignatureName %>
			<div class="signature">
				<span class="title">Delivery Signature Name</span>:
				<span class="value">{$DeliverySignatureName}</span>
			</div>
			<% end_if %>

			<% if $DeliveryAttempts %>
			<div class="attempts">
				<span class="title">Delivery Attempts</span>:
				<span class="value">$DeliveryAttempts</span>
			</div>
			<% end_if %>
		</div>
		<% end_if %>
	<% end_loop %>
</div>
<% end_if %>