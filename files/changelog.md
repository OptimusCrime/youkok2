Youkok2 - 1.0.1-pl  (29. februar 2014)
======================================

- [Info] Legacy code removal
- [Enhanced] Systemet bruker nå nye fysiske plasseringer til filer som er raskere å hente frem.
- [Enhanced] Minify av alle lokale CSS- og JS-filer.
- [Fixed] Fjernet en del legacy kode fra systemet som var nødvendig for oppgradering.

Youkok2 - 1.0.0-pl (29. februar 2014)
=====================================

- [Info] Komplett omskrivning av hele kildekoden
- [Enhanced] Nettsiden bruker nå "mobile first"-prinsippet til Bootstrap.
- [Enhanced] Velkomstsiden er endre litt.
- [Enhanced] Linker til innlogging funker på små devices.
- [Enhanced] Informasjon på forsiden vises nå penere med Bootstrap tooltip.
- [Enhanced] Fag som ikke har noen filer vises nå i en gråere farge.
- [Fixed] Feil som gjorde at cache ble hengende igjen.
- [Fixed] Antall millisekunder siden bruker på å laste er ikke lenget cachet.

Youkok2 - 0.4.0-pl (13. oktober, 2014)
======================================

- [Info] Ny funksjonalitet
- [New] Se detaljer om en fil/link/mappe ved å høyreklikke og velge 'Detaljer'.
- [Fixed] Metode som grupperte nedlastninger på tid var helt feil.
- [Fixed] Man hadde muligheten til å favoritisere noe selv om man ikke var logget inn.
- [Enchanced] Norske navn på Highcharts grafen.
- [Enchanced] Når man logger inn returneres man nå til siden man var på.

Youkok2 - 0.3..-pl (29. september, 2014)
========================================

- [Info] Hotfix.
- [Fixed] Jeg ødela compression av js-fila.

Youkok2 - 0.3.0-pl (29. september, 2014)
========================================

- [Info] Ny funksjonalitet.
- [New] Man kan nå opprette linker som linker direkte til sider og enkeltfiler.
- [New] Det er nå mulig å laste opp .zip-arkiv til Youkok2. Innholdet av arkivet blir sjekket, og opplastning blir kun
        gjennomført om alle filene er av godkjente filtyper.
- [Enhanced] Dersom man favoriserer ett fag vises fagnavnet sammen med fagkoden på forsiden.
- [Enhanced] Bedre evaluering av filtyper når man laster opp filer.
- [Enhanced] Mer generisk lasting av eksterne/interne bibliotek.
- [Fixed] Scraper-scriptet la ikke til nye fag for 2014 høst-semesteret.
- [Fixed] Feil som gjorde at antall flagg for elementer også telte lukkede flagg.
- [Fixed] Feil som gjorde at lukking av et flag ikke ble oppdatert på grunn av cache.

Youkok2 - 0.2.1-pl (25. august, 2014)
=====================================

- [Info] Hotfix.
- [Fixed] Rar feil som gjorde at linking til zip-filer ikke fungerte.

Youkok2 - 0.2.0-pl (25. august, 2014)
=====================================

- [Info] Flere bugfixes og andre ting.
- [New] Batch/zip nedlastning av alle filer i en mappe.
- [New] Nytt søkefelt på søkesiden.
- [New] Parsetid på hver pageload vises nå i footeren.
- [Fixed] Feil som var igjen etter debugging, som gjorde at avstemningen ikke fungerte som den skulle.
- [Fixed] Feil på forsiden som gjorde at 'I dag' aldri ble markert som aktiv.
- [Fixed] Noen småfeil i cachingen.
- [Fixed] Lagt tilbake flere ikoner som ikke skulle vært fjernet.
- [Other] Skrevet om flere tekster.
- [Other] Composer brukes nå til alle eksterne libs.
- [Other] Et dusin mindre bugfixes.
- [Other] Skrivefails.
- [Other] La til Phinx og Composer under 'Teknisk om nettsiden' på 'Om'-siden.
- [Enhanced] Søkefunksjonen er nå funksjonell for brukere på mobil.

Youkok2 - 0.1.1-pl (7. august, 2014)
====================================

- [Info] Ny funksjonalitet.
- [New] Søkefunksjon.
- [New] Man kan nå fjerne favoritter fra forsiden.
- [Fixed] Hover fungerte ikke når man endret mest populære på forsiden.
- [Fixed] Hvis man har stjernet et fag vises navnet på faget i hover.
- [Fixed] Feil hvor opprettelse av flagg for å gi nytt navn til en mappe hadde "filendelse".
- [Fixed] Fikset feil i regex som strippet bort punktum før filendelser.
- [Fixed] Duplikate filer fikk feil navn.
- [Enhanced] Fjernet drøssevis av ikoner som ikke var i bruk.

Youkok2 - 0.1.0-pl (4. august, 2014)
====================================

- [Info] First Public Release.
- [Info] Fjernet påkrevd NTNU-identifisering og Wall-of-Shame-konseptet.
- [New] På forsiden står det nå hvilke mappe filer ligger i, ikke bare hvilke fag det tilhører.
- [New] Linker til nedlastninger har nå rel="nofollow", for å unngå at botter besøker dem titt og ofte.
- [New] .js- & .css-filer er nå minifiserte.
- [New] Implementert heftig caching av hele siden, som reduserer antall queries drastisk.
- [New] Når man linker til nedlastning av filer på Facebook får man nå opp informasjon om fila.
- [New] Introduserte pending karma. Dette er karma på flagg som enda ikke er stengt.
- [New] Implementerte 'Karma / Historikk' til din bruker, som viser hva du har gjort for å skaffe deg
        karmaen du har opparbeidet deg.
- [Fixed] Context meny er nå korrekt sentert på x-aksen.
- [Enhanced] Opplastning av flere filer gir nå bare en melding.
- [Enhanced] Nedlastning av filer logger nå UA.
- [Enhanced] Nedlastning av filer gir korrekt Mimetype i stede for 'octet/stream'.
- [Enhanced] Antall spørringer som kjører pr. pageload er nesten halvert på de fleste sider.
- [Enhanced] Hvis e-post ble funnet under innlogging, vil den automatisk være fylt ut om passordet var feil.
- [Enhanced] Man trenger nå bare to stemmer for godkjenning for å gå godkjent en fil/mappe.
- [Enhanced] Rapportering gir nå penere tilbakemelding
- [Other] Mange mindre tekstlige endringer.

Youkok2 - 0.0.1-c (2. mai, 2014)
================================

- [Info] Closed Charlie Version.
- [Enhanced] Meldinger ser penere ut.
- [Enhanced] Flere meldinger er skrevet om.
- [Enhanced] Om man prøver å laste opp fil med filtype som ikke er støttet får man en penere tilbakemelding om dette.
- [Enhanced] Graybox med linker til andre seksjoner av Youkok2 som man kanskje burde lese.
- [Enhanced] Dersom en mappe i kokeboka er tom får man en fin tilbakemelding om dette.
- [Enhanced] Tab-indenting gjort om til space-identing i javascript-filer.
- [Enhanced] 404-side.
- [New] Man mottar nå e-post ved registrering.
- [New] Dersom det ikke er mulig å koble til databasen får man en fin melding om dette.
- [New] Filer av filtyper som vi ikke har ikoner for vil nå ha et "Unknown"-ikon istede for ingenting.
- [New] Dynamiske titler for hele nettsiden.
- [New] Google Analytics!
- [Fixed] Flere feil tilknyttet filer og url-encoding.
- [Fixed] Understeker og bindestreker i filnavn blir nå bevart korrekt.
- [Fixed] Dersom fagnavn og fagkoder blir lastet på nytt blir localStoreage tømt, slik at man ikke har mellomlagret 
          gamle verdier.
- [Fixed] Fikset feil hvor nettleseren ville oppdatere siden før alle filene var ferdige med å bli lastet opp.
- [Fixed] Dersom en fil hadde null som mimetype ble opplastningen avsluttet. Nå sjekkes filtyper før feilmelding 
          returneres.
- [Fixed] Unicode-problemer i denne fila.
- [Fixed] Unicode-problemer i e-poster.
- [Fixed] En 404-side gir nå 404 HTTP status også.
- [Changed] Datoer er nå pene og norske, ikke lenger SQL-format.
- [Changed] Når man logger inn blir man ikke lenger sendt til forsiden.
- [Changed] Rekkefølgen og innholdet i headeren.
- [Other] Masse andre mindre greier.

Youkok2 - 0.0.1-b (22. april, 2014)
===================================

- [Info] Closed Beta Version.
- [New] Det er nå mulig for brukere å endre passord.
- [New] Det er nå mulig for brukere å endre sin informasjon.
- [New] Det er mulig for brukere å verifisere gjennom sin NTNU-epost.
- [Fixed] Context-menyen sto overalt i arkivet.
- [Enhanced] Context-menyen lukkes om man venstreklikker og den er åpen.
- [Fixed] Masse styling-ting i kokeboka.
- [Fixed] Om man er bannet eller har 0 i karma får man beskjed om dette.
- [Fixed] Når man har opprettet et flagg eller sendt inn en rapport får man finere tilbakemelding om dette.
- [New] Bedre meldinger og tilbakemeldinger både server-side og client-side.
- [Enhanced] Man må nå godkjenne retningslinjene når man registerer seg.
- [New] Flatpage for karma.
- [Enhanced] Dropdown-menyen for innlogging er pyntet opp.
- [Fixed] Headeren ble helt herpa når man resizet viewporten.
- [New] Filstørrelser blir lagret i databasen.
- [Enhanced] Karma-verdien vises når man holder musa over verdien i headeren.
- [Fixed] Man kan nå submitte et søk, selv om dette ikke er ferdig utviklet enda.
- [New] Fylt ut forskjellige grayboxes rundt om på siden. Disse er ikke ferdige enda.
- [Changed] /kokebok/ er nå /kokeboka/.
- [Other] Skrev om nesten alle tekstene på siden.
- [Other] Masse, masse feilrettinger.

Youkok2 - 0.0.1-a (18. april, 2014)
===================================

- [Info] Closed Alpha Version
- [Fixed] Context meny i arkivet viste valg man ikke hadde rettigheter til å benytte.
- [Fixed] Navnet på et arkiv hadde stjerne ved siden av seg når man ikke var logget inn.
- [New] Mulighet til å lage mapper.
- [New] Mulighet til å laste opp filer.
- [New] Historikk hentes for arkivet.
- [New] Glemt passord-funksjonalitet.
- [New] Når et flagg blir godkjent/avvist gis det nå karma til alle brukere som skal ha de.
- [Enhanced] Måten login-formen blir submitta på.
- [Enhanced] Design på meldings-boksene, samt kryss for å lukke meldingen og autoclose etter x antall sekunder.
- [New] Mulighet for å registrere seg på siden.
- [Fixed] 'Mine siste nedlastninger' og 'Mest populære' arrangerte filer i feil rekkefølge.
- [New] Implementasjon av 'Slett'- og 'Nytt navn'-flagg.
- [Other] Masse, masse andre fixes og forbedringer.

Youkok2 - 0.0.1-dev (14. april, 2014)
=====================================

- [Info] Closed Developer Version
- [New] Denne fila.