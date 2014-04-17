[[+include file="header.tpl"]]

<div class="col-md-8">
	<h1>Logg inn</h1>
	<form action="logg-inn" method="post">
		<label for="login2-email">E-post</label>
		<input type="email" name="login2-email" class="form-control" id="login2-email" value="" placeholder="E-post" />
		<label for="login2-pw">Passord</label>
		<input type="password" name="login2-pw" class="form-control" id="login2-pw" value="" placeholder="Passord" />
		<button type="submit" class="btn btn-default">Logg inn</button> eller <a href="registrer">Registrer deg</a>.
	</form>
</div>

[[+include file="footer.tpl"]]