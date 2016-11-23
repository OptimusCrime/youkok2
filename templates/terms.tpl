[[+include file="header.tpl"]]
            <div>
                <div class="col-xs-12 col-md-8">
                    <h1>Retningslinjer</h1>
                    <p>Både skaperne og bidragsyterne av Youkok2 jobber mot et felles mål.</p>
                    <div class="well">
                        <p><em>Vårt mål er å lage den største og beste kokeboka på nettet. Vi ønsket et stort bibliotek av god kvalitet. Vi ønsker også at Youkok2 kan bli en ressurs man kan stole på.</em></p>
                    </div>
                    <p>For å gjøre dette mulig har vi noen regler og retningslinjer for som hjelper oss å bedre definere hva dette betyr.</p>
                    <h3>Opphavsrett</h3>
                    <p>Dette er det viktigste punktet. På Youkok2 ønsker vi ikke å ha innhold som bryter med opphavsretten. Dette kan potensielt føre til Youkok2s nedleggelse. Legg derfor aldri ut filer av bøker, tekster, eller annet materiell hvor opphavsretten brytes. Dersom du ser slike filer ønsker vi at du <a href="[[+path_for name="help"]]">rapporterer</a> den så fort som mulig.</p>
                    <h3>Vær hjelpsom</h3>
                    <p>Legg ut gode løsningsforslag, fasiter, tips og ressurser. Ikke fyll opp siden med nisjeting, feilaktig informasjon eller annet uønsket materiell. Styr unna ting av lav kvalitet. Gjennom prosessen med å godkjenne filer kan det tenkes disse filene også blir avvist.</p>
                    <h3>Ikke vær en idiot</h3>
                    <p>Som anonym bruker må alle bidrag evaluere av en administrator før de dukker opp på siden. Tingene som er nevnt ovenfor blir derfor vurdert før et bidrag eventuelt blir publisert.</p>
                    <p>Om man er en registrert bruker setter vi mer lit brukerne og håper de følger disse punktene. Ikke misbruk denne tillitten. Ved idiot-oppførsel kan din konto bli stengt. Din konto kan også bli inaktiv om du får
                    <p>Regelen er ganske enkel: Vær snill og hjelpsom, så burde du ikke ha noen problemer.</p>
                    <h3>Godkjente filtyper</h3>
                    <p>Her er en liste for godkjente filtyper på Youkok2. Dersom du har ønsker om nye filtyper kan du sende en
                    <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">e-post</a>. Vi har en begrenset liste for å minimere sjanser for virus.</p>
                    <p><b>Fil-endelser:</b></p>
                    <ul>
                    [[+foreach $ACCEPTED_FILEENDINGS as $file_ending]]    <li>.[[+$file_ending]]</li>
                    [[+/foreach]]</ul>
                </div>
                <div id="sidebar" class="col-xs-12 col-md-4">
[[+include file="sidebar_flat.tpl"]]
[[+include file="sidebar.tpl"]]
                </div>
            </div>
[[+include file="footer.tpl"]]
[[+include file="sidebar_templates.tpl"]]
</body>
</html>
