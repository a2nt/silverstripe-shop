<div class="order-status">
	<div class="reference">
		<span class="title">Order Reference:</span>
		<span class="value">#{$Reference}</span>
	</div>
	<div class="status">
		<span class="title">Order Status:</span>
		<span class="value">$Status</span>
	</div>
	<div class="progress progress-striped progress-{$Status}">
		<div class="bar bar-danger payment">Payment</div>
		<div class="bar bar-warning processing">Processing</div>
		<div class="bar sent">Delivery<% if $Status == "Sent" %> in progress<% end_if %></div>
		<div class="bar bar-success compleate">Complete</div>
	</div>
</div>