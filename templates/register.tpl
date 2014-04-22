[[+include file="header.tpl"]]

<div class="col-md-8">
	<h1>Registrer</h1>
	<p>Registrering på Youkok2 er gratis og åpen for alle. Bare fyll ut feltene under, så har du din egen konto</p>
	<p>Legg riktignok merke til at det krever en e-post som ikke finnes i systemet vårt fra før.</p>

	<hr />

	<form action="" method="post" id="register-form" name="register-form">
		<div class="form-group">
			<label for="register-form-email">E-post <span style="color: red;">*</span></label>
			<input type="email" class="form-control" id="register-form-email" name="register-form-email" placeholder="Skriv din e-post her" />
			<p><span id="register-form-email-error1">Dette krever en gyldig e-post</span> og <span id="register-form-email-error2">e-posten kan ikke være i våre systemer fra før.</span></p>
		</div>

		<div class="form-group">
			<label for="register-form-nick">Kallenavn</label>
			<input type="text" class="form-control" id="register-form-nick" name="register-form-nick" placeholder="Skriv ønsket kallenavn her" />
			<p>Dette feltet er ikke påkrevd, la det stå tom for <em>Anonym</em>.</p>
		</div>

		<div class="form-group">
			<label for="register-form-password1">Passord <span style="color: red;">*</span></label>
			<input type="password" class="form-control" id="register-form-password1" name="register-form-password1" placeholder="Ditt passord her" />
			<p>Dette feltet er naturligvis påkrevd. <span id="register-form-password-error1">Minimumslengde på 7 tegn</span>, blir hashet i databasen.</p>
		</div>

		<div class="form-group">
			<label for="register-form-password2">Gjennta passord <span style="color: red;">*</span></label>
			<input disabled type="password" class="form-control" id="register-form-password2" name="register-form-password2" placeholder="Gjennta ditt passord her" />
			<p id="register-form-password-error2">Det er en fordel om de to passord-feltene er like.</p>
		</div>

		<button type="submit" disabled id="register-form-submit" class="btn btn-default">Registrer</button>  eller <a href="">gå tilbake til forsiden</a>.
	</form>
</div>
<div class="col-md-4">
	<div class="archive-sidebar">
		<h3>Visste du at?</h3>
		<p>Det skal mye til for å lage en kortere registreringsprosess enn dette her!</p>
	</div>

	<div id="archive-sidebar-newest" class="archive-sidebar">
		<h3>Nyeste filer</h3>
		<div id="archive-sidebar-newest-inner">
			<p>Laster...</p>
		</div>
	</div>

	<div id="archive-sidebar-last-downloads" class="archive-sidebar">
		<h3>Siste nedlastninger</h3>
		<div id="archive-sidebar-last-downloads-inner">
			<p>Laster...</p>
		</div>
	</div>
</div>

[[+include file="footer.tpl"]]