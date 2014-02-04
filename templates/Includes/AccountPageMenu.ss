<div class="nav">
	<div class="navbar">
		<div class="navbar-inner">
			<span class="brand">$Title</span>
			<ul class="nav">
				<% loop AccountMenu %>
					<li class="$Status">
						<a href="$Link" rel="nofollow">
							$Title
						</a>
					</li>
				<% end_loop %>
			</ul>
		</div>
	</div>
</div>