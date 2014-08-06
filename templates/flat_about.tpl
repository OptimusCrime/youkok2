[[+include file="header.tpl"]]

<div class="col-md-8">
    <h1>Om Youkok2</h1>

    <div class="well"><p>Kok først når du har fått gryte. ~ <em>Youkok.com</em></p></div>

    <h3>Den originale Kokeboka</h3>
    <p>Som student ved NTNU har du mest sannsynlig hørt om begrepet å <em>koke</em>. Dette kommer fra Kristians Kokebok, bedre kjent som <a href="http://www.youkok.com" target="_blank">Youkok.com</a>. Denne nettsiden har vært en fantastisk ressurs for flere tusen studenter i en knipen studenthverdag.</p>
    <p>Vi har selv vært store fan av denne kokeboka, men følte at den kunne forbedres ytterligere.</p>

    <h3>Å definere bidragsmodellen</h3>
    <p>Vi følte vi kunne forbedre hvordan "gamle" Youkok la ut sine ressurser. På "gamle" Youkok er det Christian som administrerer og legger ut filer til nedlastning. Selv om dette i og for seg fungerer bra, er det naturlig at det ikke er like aktuelt å holde alt oppdatert lenge etter at sin egen studietid er forbi.</p>
    <p>Vi ønsket derfor å åpne for andre brukere som står fritt til å bidra på nettsiden.</p>

    <h3>Bidragsmodellen</h3>
    <p>Modellen vi til slutt endte opp med er i bunn og grunn svært enkel. Alle som skal gjøre noe som helst på nettsiden må <a href="registrer">registrere</a> seg på Youkok2.</p>
    <p>Når man er registrert står man fritt til å laste opp filer (så lenge det ikke bryter med våre <a href="retningslinjer">retningslinjer</a>, så klart), opprette mapper og stemme på aktive flagg. Flagg er enten forslag om godkjenning, sletting, flytting eller navnendring av filer og mapper. Når man oppretter en fil eller mappe i systemet blir et flagg for godkjenning automatisk opprettet. De andre flaggene blir manuelt opprettet av brukere dersom de syntes det er på sin plass.</p>
    <p>Du kan stemme på flagg som ikke er opprettet av deg selv. Antall stemmer for godkjenning vises som en progressbar, og antall stemmer for avvisning er skjult. Dersom et flagg får fem stemmer for avvisning går ikke forslaget igjennom, og personen som foreslo det vil motta negativ karma. Ved fem stemmer for godkjenning blir forslaget utført av systemet. Les mer om <a href="karma">karma</a> og hvilke ting som gir deg positiv og negativ uttelling.</p>

    <h3>Teknisk om nettsiden</h3>
    <p>Backend er skrevet i sin helthet i PHP. Av rammeverk, snippets og annet har vi følgende:</p>
    <ul>
        <li><a href="http://www.php.net/manual/en/book.pdo.php" target="_blank">PHP Data Objects</a></li>
        <li><a href="http://www.smarty.net" target="_blank">Smarty</a></li>
        <li><a href="https://github.com/Synchro/PHPMailer" target="_blank">PHPMailer</a></li>
        <li><a href="https://github.com/matthiasmullie/minify" target="_blank">Minify</a></li>
    </ul>
    <ul>
        <li><a href="http://getbootstrap.com" target="_blank">Bootstrap</a></li>
        <li><a href="http://bootswatch.com" target="_blank">Bootswatch Lumen</a></li>
        <li><a href="http://jquery.com" target="_blank">jQuery</a> &amp; <a href="https://jqueryui.com" target="_blank">jQuery UI</a></li>
        <li><a href="http://momentjs.com" target="_blank">Moment.js</a></li>
        <li><a href="http://twitter.github.io/typeahead.js/" target="_blank">Typeahead.js</a></li>
        <li><a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">FontAwesome</a></li>
        <li><a href="https://github.com/blueimp/jQuery-File-Upload" target="_blank">jQuery-File-Upload</a></li>
    </ul>

    <p>I tillegg er ikoner for filtyper og noe inspirasjon hentet fra <a href="http://www.mollify.org" target="_blank">Mollify</a>.</p>
</div>
<div class="col-md-4">
    <div id="archive-sidebar-readalso" class="archive-sidebar">
        <h3>Les også</h3>
        <ul>
            <li><a href="retningslinjer">Retningslinjer</a></li>
            <li><a href="karma">Karma</a></li>
            <li><a href="hjelp">Hjelp</a></li>
            <li><a href="privacy">Privacy</a></li>
        </ul>
    </div>
    
    <div id="archive-asd" class="archive-sidebar">
        <h3>Reklame</h3>
        <p>Herpaderp</p>
    </div>

    <div id="archive-sidebar-numbers" class="archive-sidebar">
        <h3>Ting</h3>
        <div id="archive-sidebar-numbers-inner">
            <p>Laster...</p>
        </div>
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