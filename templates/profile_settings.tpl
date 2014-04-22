[[+include file="header.tpl"]]

<div class="col-md-12">
	<h1>Innstillinger</h1>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#oversikt" data-toggle="tab">Oversikt</a></li>
		<li><a href="#informasjon" data-toggle="tab">Endre informasjon</a></li>
		<li><a href="#passord" data-toggle="tab">Endre passord</a></li>
		<li><a href="#identifiser" data-toggle="tab">Aktiver NTNU-bruker</a></li>
	</ul>
</div>

<div class="clear"></div>

<div class="tab-content">
  <div class="tab-pane active" id="oversikt">
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
		<div class="clear"></div>
	</div>
	<div class="tab-pane" id="informasjon">
		
	</div>
	<div class="tab-pane" id="passord">
		<div class="col-md-6">
			<form action="" method="post" id="forgotten-password-new-form" name="forgotten-password-new-form">
				<input type="hidden" value="password" name="source" />
				<div class="form-group">
					<label for="forgotten-password-new-form-oldpassword">Gammelt passord <span style="color: red;">*</span></label>
					<input type="password" class="form-control" id="forgotten-password-new-form-oldpassword" name="forgotten-password-new-form-oldpassword" placeholder="Ditt gamle passord her" />

					<label for="forgotten-password-new-form-password1">Nytt passord <span style="color: red;">*</span></label>
					<input type="password" class="form-control" id="forgotten-password-new-form-password1" name="forgotten-password-new-form-password1" placeholder="Ditt nye passord her" />
					<p>Dette feltet er naturligvis påkrevd. <span id="forgotten-password-new-form-password-error1">Minimumslengde på 7 tegn</span>, blir hashet i databasen.</p>
				</div>

				<div class="form-group">
					<label for="forgotten-password-new-form-password2">Gjennta nytt passord <span style="color: red;">*</span></label>
					<input disabled type="password" class="form-control" id="forgotten-password-new-form-password2" name="forgotten-password-new-form-password2" placeholder="Gjennta ditt nye passord her" />
					<p id="forgotten-password-new-form-password-error2">Det er en fordel om de to passord-feltene er like.</p>
				</div>

				<button type="submit" disabled id="forgotten-password-new-form-submit" class="btn btn-default">Send</button>
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<div class="tab-pane" id="identifiser">
		<p>KOmmer</p>
	</div>
</div>

[[+include file="footer.tpl"]]