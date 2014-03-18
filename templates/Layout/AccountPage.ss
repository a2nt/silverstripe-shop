<div id="MainContentContainer">
	<% include AccountPageMenu %>
	
	<div class="memberdetails typography">
		<ul>
		<% with Member %>
			<li><%t AccountPage.Name 'Name' %>: <strong>$Name</strong></li>
			<li><%t AccountPage.Email 'Email' %>: <strong>$Email</strong></li>
			<li><%t AccountPage.MemberSince 'Registered' %>: <strong>$Created.Nice</strong></li>
			<li><%t AccountPage.LastVisit 'Last visit' %>: <strong>$LastVisited.Nice</strong></li>
			<li>
				<%t AccountPage.NumberOfOrders 'Total orders' %>:
				<strong><% if PastOrders %>$PastOrders.Count<% else %>0<% end_if %></strong>
			</li>
		<% end_with %>
		</ul>
	</div>
	<a></a>
	<a href="Security/logout" class="btn btn-warning">
		<span class="icon icon-off"></span>
		<%t AccountPage.LogOut 'Log out' %>
	</a>
	<div id="PageContainer">
		<div id="ContentContainer" class="typography">
			$Content
		</div>
	</div>
</div>