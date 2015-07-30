[[+include file="header.tpl"]]
            <div class="row">
                <div class="col-xs-12 col-md-8">
                    <h1>Logg inn</h1>
                    <p>Fyll inn din e-post og ditt passord for å logge inn på Youkok2.</p>
                    <p>Dersom du har glemt ditt passord kan du prøve å resette passordet ditt <a href="glemt-passord">her</a>.
                    <form action="logg-inn" method="post">
                        <label for="login2-email">E-post</label>
                        <input type="email" name="login2-email" class="form-control" id="login2-email" value="[[+if isset($LOGIN_EMAIL)]][[+$LOGIN_EMAIL]][[+/if]]" placeholder="E-post" />
                        <label for="login2-pw">Passord</label>
                        <input type="password" name="login2-pw" class="form-control" id="login2-pw" value="" placeholder="Passord" />
                        <button type="submit" class="btn btn-default">Logg inn</button> eller <a href="registrer">registrer deg</a>.
                    </form>
                </div>
                <div id="sidebar" class="col-xs-12 col-md-4">
[[+include file="sidebar.tpl"]]
                </div>
            </div>
[[+include file="footer.tpl"]]
</body>
</html>