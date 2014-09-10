[[+include file="header.tpl"]]

<div class="col-md-8">
    <h1>Glemt passord</h1>
    <p>Skriv inn e-posten du registrerte deg med i feltet under. Skjemaet sender deg en e-post.</p>
    <p><a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt oss</a> dersom du har glemt hvilke e-post du registrerte deg med, eller opplever andre problemer.</p>

    <form action="glemt-passord" method="post">
        <label for="forgotten-email">E-post</label>
        <input type="email" name="forgotten-email" class="form-control" id="forgotten-email" value="" placeholder="E-post" />
        <button type="submit" class="btn btn-default">Send inn</button> eller <a href="logg-inn">gå tilbake</a>.
    </form>
</div>
<div class="col-md-4">
    <div id="archive-sidebar-numbers" class="archive-sidebar">
        <h3>Ting</h3>
        <div id="archive-sidebar-numbers-inner">
            <p>Laster...</p>
        </div>
    </div>

    <div id="archive-sidebar-newest" class="archive-sidebar">
        <h3>Nyeste elementer</h3>
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