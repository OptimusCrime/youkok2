[[+include file="header.tpl"]]

<div class="row">
    <div class="col-xs-12 col-md-8" id="archive-top">
        <ol class="breadcrumb" id="archive-breadcrumbs">
            <li><a href="[[+$SITE_RELATIVE]]">Hjem</a></li>
            [[+$ARCHIVE_BREADCRUMBS]]
        </ol>

        <div id="archive-title">
            <h1>Emner</h1>
        </div>

        [[+$ARCHIVE_DISPLAY]]
    </div>
    <div class="col-xs-12 col-md-4">
        <div id="archive-help" class="archive-sidebar">
            <h3>Hjelp</h3>
            <p>Fagene står alfabetisk sortert etter fagkode.</p>
            <p>Du kan også bruke søkefeltet for å søke etter fagkoder og fagnavn.</p>
            <p>Dersom du savner et fag i listen kan du <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">kontakte oss</a>, så legger vi det til.</p>
        </div>

        <div id="archive-asd" class="archive-sidebar">
            <h3>Reklame</h3>
            <p>Herpaderp</p>
        </div>
    </div>
</div>

[[+include file="footer.tpl"]]