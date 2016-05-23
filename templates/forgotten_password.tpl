[[+include file="header.tpl"]]
            <div class="row">
                <div class="col-xs-12 col-md-8">
                    <h1>Glemt passord</h1>
                    <p>Skriv inn e-posten du registrerte deg med i feltet under. Skjemaet sender deg en e-post.</p>
                    <p><a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt oss</a> dersom du har glemt hvilke e-post du registrerte deg med, eller opplever andre problemer.</p>
                    <form action="[[+TemplateHelper::urlFor('auth_forgotten_password')]]" method="post">
                        <label for="forgotten-email">E-post</label>
                        <input type="email" name="forgotten-email" class="form-control" id="forgotten-email" value="" placeholder="E-post" />
                        <button type="submit" class="btn btn-default">Send inn</button> eller <a href="logg-inn">gå tilbake</a>.
                    </form>
                </div>
                <div id="sidebar" class="col-xs-12 col-md-4">
[[+include file="sidebar.tpl"]]
                </div>
            </div>
[[+include file="footer.tpl"]]
[[+include file="sidebar_templates.tpl"]]
</body>
</html>
