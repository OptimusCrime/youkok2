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
					<span id="home-most-popular-selected">[[+if $HOME_MOST_POPULAR_DELTA == 0]]
						Denne uka
					[[+else if $HOME_MOST_POPULAR_DELTA == 1]]
						Denne måneden
					[[+else if $HOME_MOST_POPULAR_DELTA == 2]]
						Dette året
					[[+else]]
						Alltid
					[[+/if]]</span> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" id="home-most-popular-dropdown">
						<li[[+if $HOME_MOST_POPULAR_DELTA == 0]] class="disabled"[[+/if]]><a data-delta="0" href="#">Denne uka</a></li>
						<li[[+if $HOME_MOST_POPULAR_DELTA == 1]] class="disabled"[[+/if]]><a data-delta="1" href="#">Denne måneden</a></li>
						<li[[+if $HOME_MOST_POPULAR_DELTA == 2]] class="disabled"[[+/if]]><a data-delta="2" href="#">Dette året</a></li>
						<li[[+if $HOME_MOST_POPULAR_DELTA == 3]] class="disabled"[[+/if]]><a data-delta="3" href="#">Alltid</a></li>
					</ul>
				</div>
			</h2>
		</div>
		<ul class="list-group" id="home-most-popular">
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