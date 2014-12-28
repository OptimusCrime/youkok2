[[+include file="header.tpl"]]

<div class="row">
    <div class="col-xs-12 col-md-8">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb" id="archive-breadcrumbs">
                    <li><a href="[[+$SITE_RELATIVE]]">Hjem</a></li>
                    [[+$ARCHIVE_BREADCRUMBS]]
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 center">
                <h1>Emner</h1>
            </div>
        </div>
        <div class="row">
            [[+$ARCHIVE_DISPLAY]]
        </div>
    </div>
    <div id="sidebar" class="col-xs-12 col-md-4">
        <div class="sidebar-element">
            <h3>Hjelp</h3>
            <div class="sidebar-element-inner">
                <p>Fagene står alfabetisk sortert etter fagkode.</p>
                <p>Du kan også bruke søkefeltet for å søke etter fagkoder og fagnavn.</p>
                <p>Dersom du savner et fag i listen kan du <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">kontakte oss</a>, så legger vi det til.</p>
            </div>
        </div>
    </div>
</div>

[[+include file="footer.tpl"]]