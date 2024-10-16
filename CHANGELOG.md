Youkok2 - 6.0.3-pl     (17. oktober 2024)
=========================================

- [Info] Fixxxes
- [Fix] Caching forblir et av universets største utfordringer...

Youkok2 - 6.0.2-pl     (17. oktober 2024)
=========================================

- [Info] Fixxxes
- [Fix] Problemer med caching av mest populære filer.
- [Improvement] Fjernet litt ubrukt kode og ryddet opp litt.

Youkok2 - 6.0.1-pl      (5. oktober 2024)
=========================================

- [Info] Fixxes
- [Fix] Vise bare 10 siste besøkte fag.
- [Fix] Forenkle mest populære fag.

Youkok2 - 6.0.0-pl   (23. september 2024)
=========================================

- [Info] Fixxes
- [Change] MySQL -> Postgres
- [Upgrade] PHP 7.4 -> PHP 8.3
- [Upgrade] Slim 3 -> Slim 4
- [Upgrade] Node 12 -> Node 18 (kunne godt ha oppgradert mer, men fikk problemer med noen deps)
- [Removal] Fjernet oppsettet med migrasjoner helt, fordi det ikke var nødvendig.
- [Removal] Fjernet pools, som var totalt unødvendig.
- [Removal] Alle spørringer henter nå alle attributter i `elements`-tabellen.
- [Removal] Variabler på model-klassene, som bare lagde problemer.
- [Improvement] Forbedret logging av feilmeldinger i stede for å legge exceptions til default error
                handler.
- [Improvement] Forenklet caching drastisk.
- [Improvement] Caching av hele output-payload, for å redusere unødvendig arbeid.
- [Change] Drastisk forenklet og redusert hvordan vi logger nedlastninger, som gjør at databasen
           blir mye mindre, og spørringene blir enklere.
- [Fix] Antall nedlastninger per fag reflekteres nå i sanntid.
- [New] Implementert enklere system for å logge antall nedlastninger per dag, uke, mnd, og år.
- [New] Nedtelling på toppen av siden til sunset av Youkok2.com.

Youkok2 - 5.0.1-pl         (3. mars 2021)
=========================================

- [Info] Bugfix
- [Fixed] Cronjobs
- [Improved] Building Docker images should be faster due to cached layers.

Youkok2 - 5.0.0-pl     (23. februar 2021)
=========================================

- [Info] Ytelsesforbedringer
- [Stuff] Bytte ut php-apache med php-fpm.
- [Stuff] Bruke nginx for å serve statisk innhold.
- [Removed] Sessions. Brukerinnstillinger lagres heller i localStorage.
- [Stuff] Forenkling av kodebase.

Youkok2 - 4.2.0-pl     (18. februar 2020)
=========================================

- [Info] Features og bugfixes.
- [Fixed] Søkefeltet viser resultater etter søk på bare to bokstaver i stede for tre.
- [Fixed] Man kan nå søke med STORE bokstaver.
- [Stuff] Opprydd i ymse sloppy kode.
- [Admin] Opplistning av Redis-cache.
- [Admin] Mulighet til å søke opp og redigere enkeltfag, selv de uten innhold.

Youkok2 - 4.1.0-pl     (17. februar 2020)
=========================================

- [Info] Features.
- [Improved] La til funksjonalitet for å markere et fag med innhold som er ønsket slettet.
- [Fixed] Admin kan nå redigere fag igjen.

Youkok2 - 4.0.5-pl      (4. februar 2020)
=========================================

- [Info] Improvements.
- [Improved] Bedre håndtering og redirecting av gamle linker som støyer veldig i loggene.

Youkok2 - 4.0.4-pl      (3. februar 2020)
=========================================

- [Info] Improvements.
- [Improved] Siden ser og fungerer bedre på mobile enheter nå. Perfekt er det ikke, men det
             er en liten forbedring fra tidligere.

Youkok2 - 4.0.3-pl      (3. februar 2020)
=========================================

- [Info] Bugfix.
- [Fixed] Nye fag er ikke lenger cachet. Antallet blir resatt og telt på nytt hver natt.
- [Fixed] Oversikten over fag viser nå piler til neste sider hvis man står på side en og det er
          flere fag på neste side.

Youkok2 - 4.0.2-pl      (28. august 2019)
=========================================

- [Info] Bugfix.
- [Fixed] Fikset faktisk opplisting av fag.
- [Fixed] Ikke legg til nedlastninger gjort av admin.
- [Fixed] Endre rettigheter på cache-filer slik at ikke root eier disse.
- [Fixed] Migrasjon for å legge til nye indexes.
- [Technical] Endret hvordan mounting av filer gjøres på systemet.
- [Technical] Splittet opp env-filene i common, dev og prod.

Youkok2 - 4.0.1-pl      (27. august 2019)
=========================================

- [Info] Bugfix.
- [Fixed] Forsøk på å fikse opplisting av fag.

Youkok2 - 4.0.0-pl      (26. august 2019)
=========================================

- [Info] Release.
- [Changed] Endret versjonsnummeret.

Youkok2 - 4.0.0-alpha   (22. august 2019)
=========================================

- [Info] Alpha for testing.
- [Info] Total omskrivning, igjen.
- [Technical] Bruker dependency injection fremfor builder patterns.
- [Technical] Drastisk forenklet kodebasen.
- [Technical] Forsiden, faglisten, arkivet og boksene i sidemenyen er nå skrevet i React.
- [Technical] Skrevet oss bort fra masse bottlenecks som gjorde siden veldig treg. Spesielt forsiden.
- [Removed] Favoritter og mine siste besøkte fag på forsiden.
- [New] Vise siste nedlastninger på forsiden.
- [Fixed] Forsiden viser nå faktiske nedlastninger for hver dag, uke, måned, år og alltid.

Youkok2 - 3.0.0-pl    (19. november 2017)
=========================================

- [Info] Total omskrivning.
- [Technical] Skrevet om hele nettsiden til Request/Response pattern i Slim3.
- [Technical] Caching bruker nå Redis key-value storage.
- [Technical] Docker-basert arkitektur, både i produksjon og i utvikling.
- [Removed] Brukere og all logikk knyttet til innlogging og registrering.
- [Removed] Mine siste nedlastninger.
- [Removed] Karma og alt det surret der.
- [Removed] Mulighet for brukere til å selv opprette mapper.
- [New] Session-basert cookie opplegg.
- [New] Mine siste besøkte fag.
- [Enhanced] Bedre søk og bedre highlight
- [Fixed] Opplastede filer vil ikke miste filtypen i URIen sin.
- [Enhanced] Forsiden har fått en mindre overhalling.
- [Enhanced] Nytt/Bedre innhold i sidebar.
- [Enhanced] Opplisting av mapper og filer bruker nå en listevisning i stede for rutenett.
- [New] Skrevet om alle statiske sider.

Youkok2 - 2.4.1-pl       (29. april 2016)
=========================================

- [Info] Bigfixes
- [Fixed] Messages knakk...igjen.
- [Fixed] Man kan nå bruke piltastene til å velge fag i søkeboksen.

Youkok2 - 2.4.0-pl       (29. april 2016)
=========================================

- [Info] Bigfix and various
- [Fixed] La til filendelse i nedlastningsurl for filer.
- [Fixed] Linker til filer og redirects har ikke lenger en / på slutten av adressen.
- [Fixed] Linker på forsiden til fag åpnet ny fane.
- [Fixed] Rar spørring på personlige, tidligere nedlastninger som gjorde at enkelte nedlstninger forsvant.
- [Enhanced] Meldinger kan nå ha wildcards.

Youkok2 - 2.3.1-pl       (29. april 2016)
=========================================

- [Info] Bigfix
- [Fixed] Nye nedlastninger manglet timestamp.

Youkok2 - 2.3.0-pl       (27. april 2016)
=========================================

- [Info] Rewrites, Unit tests, bugfixes
- [Enhanced] Skrevet om hvordan URLer blir parset.
- [Enhanced] Skrevet om hvordan Unit tester blir kjørt.
- [Fixed] Mindre feil som følger av ny struktur av databasen.
- [Fixed] Småfeil som kunne resultere i en notice error.
- [Fixed] Opplastning av filer sletter cache om foreldre er tom.
- [Fixed] Beskrive av en mappe i arkivet.
- [Fixed] Filer som ikke er godkjent eller som er skjulte vil være skjulte over alt i systemet nå.
- [New] Implementert system for reverse URL oppslag, for å minimere sjansene for feil URLer i systemet.

Youkok2 - 2.2.0-pl     (21. februar 2016)
=========================================

- [Info] Bugfixes og ymse
- [Fixed] Småplukk på stylingen i arkivet.
- [Enhanced] Gråboksene i sidemenyen ser penere ut før de har lastet innholdet sitt.
- [Enhanced] Forbedret måten caching blir gjort på.
- [New] Implementert "scheduled" jobber for å avlaste systemet fra tyngre spørringer.
- [New] Støtte for HTTPS.
- [New] Funksjonalitet for å deklarere et element som en alias for et annet element. Kan f.eks. brukes til fag som har enten
        byttet fagkode eller navn.
- [New] På forsiden vises nå de siste 15 besøkte fagene.

Youkok2 - 2.1.1-pl     (1. desember 2015)
=========================================

- [Info] Bugfixes
- [Fixed] Forsiden viste feil tall for populære fag og elementer.

Youkok2 - 2.1.0-pl    (29. november 2015)
=========================================

- [Info] Fixes og mest populære fag
- [Fixes] Stavet GitHub korrekt i footeren.
- [New] På forsiden vises nå de mest populære fagene.

Youkok2 - 2.0.1-pl     (3. november 2015)
=========================================

- [Info] Småfixes
- [Enhanced] Viser nå eksamensdatoer for fag som har mappearbeid.

Youkok2 - 2.0.0-pl   (20. september 2015)
=========================================

- [Info] Prettification
- [Enhanced] Flyttet alt av HTML ut av programmeringsstuff.
- [Enhanced] Vårrengjøring av ymse.
- [Enhanced] Skrevet om store deler av kildekoden.
- [Enhanced] Skrevet om caching, den burde være bedre nå.
- [Fixed] Det er nå mulig å logge inn fra 'Logg inn'-viewet og ikke bare dropdown-menyen.
- [Fixed] Korrekt brukernavn blir nå lagret i databasen.
- [Fixed] Valg av element i søkemenyen funker igjen.

Youkok2 - 1.3.4-pl         (26. mai 2015)
=========================================

- [Info] Bugfixes och søk
- [Fixed] Diverse bugs.
- [Fixed] Søk funker igjen.
- [Other] Mindre speedboost.

Youkok2 - 1.3.3-pl         (25. mai 2015)
=========================================

- [Info] Bugfixes
- [Fixed] Fikset bugs relatert til caching (igjen).
- [New] Favicon!!!!!

Youkok2 - 1.3.2-pl         (22. mai 2015)
=========================================

- [Info] Bugfixes
- [Fixed] Fikset bugs relatert til caching.

Youkok2 - 1.3.1-pl         (16. mai 2015)
=========================================

- [Info] Bugfixes
- [Fixed] Fikset nedtelling til eksamen.

Youkok2 - 1.3.0-pl         (11. mai 2015)
=========================================

- [Info] Bugfixes, meldinger og nedtelling
- [Enhanced] Backend stuff.
- [New] Statiske medlinger.
- [New] Nedtelling til eksamen!

Youkok2 - 1.2.2-pl       (11. april 2015)
=========================================

- [Info] Minor, tekst og meny
- [Removed] Privacy-siden er fjernet da den egentlig ikke ga noen mening.
- [Enhanced] Mange av tekstene er skrevet om.
- [Enhanced] Flere bibliotek som siden benytter seg av manglet under Om. Disse er nå lagt til.
- [Enhanced] Fjernet opacity over element-ikonene i kokeboka.
- [New] Reintrodusert menyen for hvert enkelt element i kokeboka. Menyen gjør ikke så mye i dag, men mye av funksjonaliteten
        som ble fjernet ved overgangen til 1.0.0 vil legges til her igjen.

Youkok2 - 1.2.1-pl     (25. februar 2015)
=========================================

- [Info] Minor og bugfixes
- [New] Når man poster en link hentes automatisk tittelen til siden og foreslår denne som navn. Man står fremdeles
        fritt til å velge en annen tittel.
- [Fixed] Feil som gjorde at nedlastninger sluttet å fungere.
- [Enhanced] Byggescript.
- [Enhanced] Fjernet flere legacy ting i databasen.

Youkok2 - 1.2.0-pl     (25. februar 2015)
=========================================

- [Info] Bugfixes og reimplementering
- [New] Man kan laste opp filer på siden igjen!
- [New] Man kan opprette mapper igjen!
- [New] Man kan poste linker igjen!
- [New] Man kan nå laste opp filer og poste linker selv om man ikke er logget inn. Disse må bli manuelt godkjent
        av en administrator før de er synlige på siden.
- [Fixed] Loggfører antall nedlastninger igjen.
- [Fixed] Redirect system som ikke fungerte helt som det skulle.
- [Fixed] Enkelte filer migrerte ikke korrekt over fra det gamle systemet.
- [Fixed] Feil dato på forrige versjon i changelogen.
- [Fixed] Nedlastning av filer åpnes nå i nytt vindu.
- [Enhanced] .pdf, .py og .txt går nå ikke direkte til nedlastning, men til visning i nettleseren.
- [Enhanced] Minifisering av flere .js-filer.
- [Enhanced] Endret noen farger og layout i arkivet.
- [Enhanced] Endret hvordan data lagres for benyttelse i javascript.
- [Removed] Batchnedlastning av filer som .zip er fjernet.
- [Other] Ble kvitt enda mer legacy kode.

Youkok2 - 1.1.0-pl      (29. januar 2015)
=========================================

- [Info] Legacy code removal
- [Enhanced] Systemet bruker nå nye fysiske plasseringer til filer som er raskere å hente frem.
- [Fixed] Fjernet en del legacy kode fra systemet som var nødvendig for oppgradering.
- [Fixed] Søk på eksisterende emner fungerte ikke.

Youkok2 - 1.0.0-pl      (29. januar 2015)
=========================================

- [Info] Komplett omskrivning av hele kildekoden
- [Enhanced] Nettsiden bruker nå "mobile first"-prinsippet til Bootstrap.
- [Enhanced] Velkomstsiden er endre litt.
- [Enhanced] Linker til innlogging funker på små devices.
- [Enhanced] Informasjon på forsiden vises nå penere med Bootstrap tooltip.
- [Enhanced] Fag som ikke har noen filer vises nå i en gråere farge.
- [Enhanced] Minify av alle lokale CSS- og JS-filer.
- [Enhanced] Mindre foredringer av headeren.
- [Fixed] Feil som gjorde at cache ble hengende igjen.
- [Fixed] Antall millisekunder siden bruker på å laste er ikke lenget cachet.

Youkok2 - 0.4.0-pl    (13. oktober, 2014)
=========================================

- [Info] Ny funksjonalitet
- [New] Se detaljer om en fil/link/mappe ved å høyreklikke og velge 'Detaljer'.
- [Fixed] Metode som grupperte nedlastninger på tid var helt feil.
- [Fixed] Man hadde muligheten til å favoritisere noe selv om man ikke var logget inn.
- [Enchanced] Norske navn på Highcharts grafen.
- [Enchanced] Når man logger inn returneres man nå til siden man var på.

Youkok2 - 0.3.1.-pl (29. september, 2014)
=========================================

- [Info] Hotfix.
- [Fixed] Jeg ødela compression av js-fila.

Youkok2 - 0.3.0-pl  (29. september, 2014)
=========================================

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

Youkok2 - 0.2.1-pl     (25. august, 2014)
=========================================

- [Info] Hotfix.
- [Fixed] Rar feil som gjorde at linking til zip-filer ikke fungerte.

Youkok2 - 0.2.0-pl     (25. august, 2014)
=========================================

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

Youkok2 - 0.1.1-pl      (7. august, 2014)
=========================================

- [Info] Ny funksjonalitet.
- [New] Søkefunksjon.
- [New] Man kan nå fjerne favoritter fra forsiden.
- [Fixed] Hover fungerte ikke når man endret mest populære på forsiden.
- [Fixed] Hvis man har stjernet et fag vises navnet på faget i hover.
- [Fixed] Feil hvor opprettelse av flagg for å gi nytt navn til en mappe hadde "filendelse".
- [Fixed] Fikset feil i regex som strippet bort punktum før filendelser.
- [Fixed] Duplikate filer fikk feil navn.
- [Enhanced] Fjernet drøssevis av ikoner som ikke var i bruk.

Youkok2 - 0.1.0-pl      (4. august, 2014)
=========================================

- [Info] First Public Release.
- [Info] Fjernet påkrevd NTNU-identifisering og Wall-of-Shame-konseptet.
- [New] På forsiden står det nå hvilke mappe filer ligger i, ikke bare hvilke fag det tilhører.
- [New] Linker til nedlastninger har nå rel="nofollow", for å unngå at botter besøker dem titt og ofte.
- [New] js- og css-filer er nå minifiserte.
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

Youkok2 - 0.0.1-c          (2. mai, 2014)
=========================================

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

Youkok2 - 0.0.1-b       (22. april, 2014)
=========================================

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

Youkok2 - 0.0.1-a       (18. april, 2014)
=========================================

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

Youkok2 -    0.0.0-dev (1. oktober, 2013)
=========================================

- [Info] Initial commit
