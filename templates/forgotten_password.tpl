[[+include file="header.tpl"]]

<div class="col-md-8">
	<h1>Glemt passord?</h1>
	<p>Skriv inn din e-post i feltet under for å få tilgang til et skjema for å endre passord.</p>
	<form action="glemt-passord" method="post">
		<label for="forgotten-email">E-post</label>
		<input type="email" name="forgotten-email" class="form-control" id="forgotten-email" value="" placeholder="E-post" />
		<button type="submit" class="btn btn-default">Send inn</button> eller <a href="logg-inn">Gå tilbake</a>.
	</form>
</div>

[[+include file="footer.tpl"]]