[[+include file="header.tpl"]]

<div class="col-md-6">
	<h1>Registrer</h1>
	<p>Hei på deg. Ønsker du å registrere deg en konto på Youkok2?</p>
	<p>Fyll ut feltene under, så er du good to go.</p>

	<hr />

	<form action="" method="post" id="register-form" name="register-form">
		<label for="register-form-email">E-post <span style="color: red;">*</span></label>
		<input type="email" class="form-control" id="register-form-email" name="register-form-email" placeholder="Skriv din e-post her*" />
		<p>Dette feltet er påkrevd og e-post kan ikke være i våre systemer fra før.</p>

		<label for="register-form-nick">Kallenavn</label>
		<input type="text" class="form-control" id="register-form-nick" name="register-form-nick" placeholder="Skriv ønsket kallenavn her" />
		<p>Dette feltet er ikke påkrevd, la det stå tom for <em>Anonym</em>.</p>

		<label for="register-form-password1">Passord <span style="color: red;">*</span></label>
		<input type="password" class="form-control" id="register-form-password1" name="register-form-password1" placeholder="Ditt passord her" />
		<p>Dette feltet er naturligvis påkrevd. Minimumslengde på 7 tegn, blir hashet i databasen.</p>

		<label for="register-form-password2">Gjennta passord <span style="color: red;">*</span></label>
		<input type="password" class="form-control" id="register-form-password2" name="register-form-password2" placeholder="Gjennta ditt passord her" />
		<p>Det er en fordel om de to passord-feltene er like.</p>

		<button type="submit" class="btn btn-default">Send</button> <span id="">Vennligst rett feilene i skjemaet før du fortsetter.</span>
	</form>
</div>

[[+include file="footer.tpl"]]