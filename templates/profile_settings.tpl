[[+include file="header.tpl"]]

<div class="col-md-12">
	<h1>Innstillinger</h1>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#">Oversikt</a></li>
		<li><a href="#">Endre informasjon</a></li>
		<li><a href="#">Endre passord</a></li>
		<li><a href="#">Aktiver NTNU-bruker</a></li>
	</ul>
</div>
<div id="profile-overview">
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Generelt</h3>
			</div>
			<div class="panel-body">
				<p><b>E-post:</b> [[+$PROFILE_USER_EMAIL]]</p>
				<p><b>Kallenavn:</b> [[+$BASE_USER_NICK]]</p>
				<p><b>Karma:</b> [[+$BASE_USER_KARMA]]</p>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Annet</h3>
			</div>
			<div class="panel-body">
				<p><b>Aktiv konto:</b> [[+if $PROFILE_USER_ACTIVE == 1]]<i class="fa fa-check" style="color: green;"></i>[[+else]]<i class="fa fa-times" style="color: red;"></i>[[+/if]]</p>
				<p><b>NTNU-aktivert:</b> [[+if $PROFILE_USER_VERIFIED == 1]]<i class="fa fa-check" style="color: green;"></i>[[+else]]<i class="fa fa-times" style="color: red;"></i>[[+/if]]</p>
			</div>
		</div>
	</div>
</div>

[[+include file="footer.tpl"]]