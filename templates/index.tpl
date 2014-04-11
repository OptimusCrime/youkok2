[[+include file="header.tpl"]]

<div class="row">
	<div class="col-md-6">
		<div class="list-header">
			<h2>Nyeste filer</h2>
		</div>
		<ul class="list-group">
			[[+$HOME_NEWEST]]
		</ul>
	</div>
	<div class="col-md-6">
		<div class="list-header">
			<h2>Mest populære
				<div class="btn-group">
					<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
					Denne måneden <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="#">Denne uka</a></li>
						<li role="presentation" class="disabled"><a role="menuitem" tabindex="-1" href="#">Denne måneden</a></li>
						<li><a href="#">Dette året</a></li>
						<li><a href="#">Alltid</a></li>
					</ul>
				</div>
			</h2>
		</div>
		<ul class="list-group">
			[[+$HOME_MOST_POPULAR]]
		</ul>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="list-header">
			<h2>Mine favoritter</h2>
		</div>
		<ul class="list-group">
			[[+$HOME_USER_FAVORITES]]
		</ul>
	</div>
	<div class="col-md-6">
		<div class="list-header">
			<h2>Mine siste</h2>
		</div>
		<ul class="list-group">
			[[+$HOME_USER_LATEST]]
		</ul>
	</div>
</div>

[[+include file="footer.tpl"]]