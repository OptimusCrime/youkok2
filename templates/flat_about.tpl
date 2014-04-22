[[+include file="header.tpl"]]

<div class="col-md-8">
	<h1>Om Youkok2</h1>

	<div class="well"><p>Kok først når du har fått gryte. ~ <em>Youkok.com</em></p></div>

	<h3>Den originale Kokeboka</h3>
	<p>Som student ved NTNU har du mest sannsynlig hørt om begrepet å <em>koke</em>. Dette kommer fra Kristians Kokebok, bedre kjent som <a href="http://www.youkok.com" target="_blank">Youkok.com</a>. Denne nettsiden har vært en fantastisk ressurs for flere tusen studenter i en knipen studenthverdag.</p>
	<p>Vi har selv vært store fan av denne kokeboka, men følte at den kunne forbedres ytterligere..</p>
	
	<h3>Å definere bidragsmodellen</h3>
	<p>Vi følte vi kunne forbedre hvordan "gamle" Youkok la ut sine ressurser. På "gamle" Youkok er det Kristian som administrerer og legger ut filer til nedlastning. Selv om dette i og for seg fungerer bra, er det naturlig at det ikke er like aktuelt å holde alt oppdatert lenge etter at sin egen studietid er forbi.</p>
	<p>Vi så på <a href="http://www.stackoverflow.com" target="_blank">StackOverflow.com</a> og hvordan deres modell gjør det mulig for hele communitiet å bidra med spørsmål, svar og administrering. Mye av vår modell er direkte stjålet eller inspirtert fra dem.</p>

	<h3>Bidragsmodellen</h3>
	<p>Modellen vi til slutt endte opp med er i bunn og grunn svært enkel. Alle som skal gjøre noe som helst på nettsiden må <a href="registrer">registrere</a> seg på Youkok2. Vi ønsket også å identifisere brukere mot NTNU for å holde idioter og troll unna. Mer om dette i neste seksjon.</p>
	<p>Når man er identifisert står man fritt til å laste opp filer (så lenge det ikke bryter med våre <a href="retningslinjer">retningslinjer</a>, så klart), opprette mapper og stemme på aktive flagg. Flagg er enten forslag om godkjenning, sletting, flytting eller navnendring av filer og mapper. Når man oppretter en fil eller mappe i systemet blir et flagg for godkjenning automatisk opprettet. De andre flaggene blir manuelt opprettet av brukere dersom de syntes det er på sin plass.</p>
	<p>Du kan stemme på flagg som ikke er opprettet av deg selv. Antall stemmer for godkjenning vises som en progressbar, og antall stemmer for avvisning er skjult. Dersom et flagg får fem stemmer for avvisning går ikke forslaget igjennom, og personen som foreslo det vil motta negativ karma. Ved fem stemmer for godkjenning blir forslaget utført av systemet. Les mer om <a href="karma">karma</a> og hvilke ting som gir deg positiv og negativ uttelling.</p>
	
	<h3>Identifisering mot NTNU &amp; Wall of Shame</h3>
	<p>Når en side er åpen slik som Youkok2 er, tiltrekkes idioter og troll fra alle hjørner av Bartebyen. Vi ønsker å gjøre Youkok2 til en ordentlig og respektert side, ikke ett sted fylt til randen av virus, kopibeskyttet materiell og feilaktig informasjon. På grunn av dette har vi innført identifisering mot NTNU for alle som skal bidra.</p>
	<p>Så lenge man oppfører seg på siden vil dette aldri ha noe å si for deg som bruker. Skulle du derimot gjøre store brudd på våre <a href="retningslinjer">retningslinjer</a>, kan man risikere å havne på Wall of Shame. Her publiseres brukere med deres NTNU-brukernavn, samt årsaken til at han eller hun har mottatt denne straffen.</p>
	<p>Igjen så er dette kun for å ta de som ønsker å ødelegge, og de som aktivt går inn for å bryte loven på Youkok2s nettsider.</p>
	<p>Det må også understrekes at man ikke trenger å registrere seg med sin NTNU e-post. Om man kun vil være et medlem som ikke bidrar kan man bruke hvilke som helst e-post og vi vil aldri publisere denne noe sted.</p>

	<h3>Teknisk om nettsiden</h3>
	<p>Backend er skrevet i sin helthet i PHP. Av rammeverk, snippets og annet har vi følgende:</p>
	<ul>
		<li><a href="http://www.php.net/manual/en/book.pdo.php" target="_blank">PHP Data Objects</a></li>
		<li><a href="http://www.smarty.net" target="_blank">Smarty</a></li>
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

	<h3>Hvem har laget denne nettsiden?</h3>
	<p>Naaahh...</p>
</div>
<div class="col-md-4">
	<div id="archive-asd" class="archive-sidebar">
		<h3>Reklame</h3>
		<p>Herpaderp</p>
	</div>

	<div id="archive-sidebar-numbers" class="archive-sidebar">
		<h3>Artige tall</h3>
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