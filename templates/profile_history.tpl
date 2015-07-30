[[+include file="header.tpl"]]

<div class="row">
    <div class="col-xs-12 col-md-8">
        <h1>Karma / Historikk</h1>
        <p>Tabellen under viser hvordan du har opparbeidet deg din karma. Rader i grønt og rødt er avsluttede flagg hvor 
        karmaen er lagt til eller fjernet permanent fra din bruker. Grønt symboliserer at ditt flagg, eller din stemme 
        har vært vellykket. Rødt symboliserer det motsatte.</p>
        <p>Rader som ikke har noen farge er pending karma, og er karma som enda ikke er permanent lagt til din bruker. 
        Denne karmaen kan bli trukket fra din bruker dersom ditt flagg blir avvist, eller din stemme ikke har vært 
        vellykket.</p>
        <ul class="list-group">
            [[+$PROFILE_USER_HISTORY]]
        </ul>
    </div>
    <div id="sidebar" class="col-xs-12 col-md-4">
        [[+include file="sidebar.tpl"]]
    </div>
</div>

[[+include file="footer.tpl"]]
</body>
</html>