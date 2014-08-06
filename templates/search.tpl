[[+include file="header.tpl"]]

<div class="col-md-8">
	<h1>Søk</h1>
	<p>Ditt søk på <strong>[[+$SEARCH_QUERY]]</strong> returnerte <strong>[[+$SEARCH_NUM]]</strong> treff.</p>
    <p>Du kan bruke stjerne (*) som wildcard når du søker. Søket vil kun treffe på fagkoder og fagnavn. Dersom et fag mangler i listen kan du <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">kontakte oss</a>, så legger vi det til.</p>

	<hr />

	[[+$SEARCH_RESULT]]
</div>
<div class="col-md-4">
    <div id="archive-sidebar-readalso" class="archive-sidebar">
        <h3>Les også</h3>
        <ul>
            <li><a href="om">Om YouKok2</li>
            <li><a href="retningslinjer">Retningslinjer</a></li>
            <li><a href="karma">Karma</a></li>
            <li><a href="hjelp">Hjelp</a></li>
        </ul>
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