[[+include file="header.tpl"]]

<div class="col-md-8">
	<h1>Om Youkok2</h1>

	<div class="well"><p>Kok først når du har fått gryte. ~ <em>Youkok.com</em></p></div>

	<h3>Den originale Kokeboka</h3>
	<p>Som student ved NTNU har du mest sannsynlig hørt om begrepet å <em>koke</em>. Dette kommer fra Kristians Kokebok, bedre kjent som <a href="http://www.youkok.com" target="_blank">Youkok.com</a>. Denne nettsiden har vært en fantastisk ressurs for flere tusen studenter i en knipen studenthverdag.</p>
	<p>Jeg liker konseptet med Youkok veldig godt, men syntes det er flere elementer som kan forbedres mye. For det første er det kun en person som driver nettsiden. Vedkommende er ikke lenger student ved NTNU og det er logisk at det blir mye kjedelig arbeid å holde den oppdatert da. Min idé var derfor å relansere Kokeboka i ny form.</p>
	
	<h3>Alle kan bidra</h3>
	<p>Om du programmerer og/eller er litt bevandret på Internett har du uten vil vært innom <a href="http://www.stackoverflow.com" target="_blank">StackOverflow.com</a>. Nettsiden har blitt kjempepopulær fordi den bygger på community-prinsippet.  Alle kan bidra! Idioter blir luket ut, og de som er flinke får mulighet til å hjelpe til mer og mer. Jeg ville bruke denne modellen sammen med Kokeboka.</p>
	
	<h3>Krav til bidragelse</h3>
	<p>Dersom det skulle være mulig å ha en slik modell måtte det innføres restriksjoner og straff for brukere som brøt med <a href="retningslinjer">retningslinjene</a> til siden. Ettersom hvem som helt kan laste opp filer må man være nøye med å luke ut filer som er beskyttet av opphavsrett osv. På grunn av dette ble det bestemt at man måtte verifisere seg gjennom en NTNU-epost. Disse e-postene slutter på <strong>@stud.ntnu.no</strong>, og gjør det dermed enkelt å identifisere de som hjelper til (selv om de selvsagt er helt anonyme om de oppfører seg).

	<h3>Karma &amp; Wall of Shame</h3>
	<p>Kun brukere som har registrert seg med sin NTNU-epost får mulighet til å bidra og stemme på siden. Man vil fortsatt være helt anonym selv om man har gjort dette.
	<p>Når man registrerer seg får man 5 karma-poeng. For hver "gode" gjerning man gjør får man 3 karmapoeng. Dersom man har stemt på feil valg mister man 1 karmapoeng. Om man har null poeng får man ikke lenger mulighet til å bidra på nettsiden.</p>
	<p>Ved store overtrap av reglementet kan man også bli direkte utestengt av siden. Ved særskilte store overtrapt kan man havne i Wall of Shame. Dette er veggen for de som ikke har klart å oppføre seg. På denne veggen vil det publiseres NTNU-brukernavnet til vedkommende og årsaken til at han eller hun ble utestengt. Vi har valgt å gjøre dette for å forsikre oss om at ingen bruker nettsidene og systemet våres til dritt og ugang.</p>

	<h3>Teknisk om nettsiden</h3>
	<p>Nettsiden i sin helhet er skrevet i PHP. Det ble vurdert noen rammeverk, men valget falt til slutt på å skrive et eget system fra bunnen av. Av andre ting og tang har vi følgende:</p>
	<ul>
		<li><b>PDO:</b> PHP Data Objects, for å snakke med databasen.</li>
		<li><b>Smarty:</b> Template engine skrevet i PHP.</li>
		<li><b>Custom Bcrypt Lib:</b> For å kryptere passord.</li>
	</ul>
	<ul>
		<li><b>Bootstrap:</b> Styling av siden. Responsive design osv.</li>
		<li><b>jQuery &amp; jQuery UI:</b> Fordi pure js er slitsomt.</li>
		<li><b>Moment.js:</b> Stress å regne med tid.</li>
		<li><b>Typeahead.js:</b> For autocomplete av fag og fagkoder i søkefeltet.</li>
		<li><b>FontAwesome:</b> Fordi bra ikoner.</li>
	</ul>

	<h3>Utviklingsprosess</h3>
	<p>Systemet i sin helhet ble programmert av en enkeltperson i løpet av påsken 2014 under varierende mengder bakfyll. Ideen til siden ble klekket mange måneder i forveien, og de første linjene med kode daterer faktisk tilbake til 1. oktober 2013. Mer eller mindre hele kildekoden har riktignok blitt skrevet om siden den gang.</p>
	<p>Gjennom hele prosessen har Git og GitHub blitt flittig brukt.</p>
</div>

[[+include file="footer.tpl"]]