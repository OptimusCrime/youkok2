[[+include file="header.tpl"]]

<div class="col-md-8">
    <h1>Admin!</h1>
    
    <p><strong>[[+$ADMIN_USERS]]</strong> registrerte brukere, <strong>[[+$ADMIN_FILES]]</strong> filer i systemet og <strong>[[+$ADMIN_DOWNLOADS]]</strong> nedlastninger hvorav <strong>[[+$ADMIN_DOWNLOADS_LAST_24]]</strong> av disse er de siste 24 timene.</p>
    <p>De <strong>[[+$ADMIN_FILES]]</strong> filene utgjør totalt <strong>[[+$ADMIN_SIZE]]</strong>. De totalt <strong>[[+$ADMIN_DOWNLOADS]]</strong> nedlastningene utgjør <strong>[[+$ADMIN_BANDWIDTH]]</strong> bandwidth forbruk.</p>
</div>
<div class="col-md-4">
    <div id="archive-sidebar-readalso" class="archive-sidebar">
        <h3>Nedlastninger pr. dag</h3>
        <ul>
            [[+$ADMIN_DOWNLOADS_PR_DAY]]
        </ul>
    </div>
</div>

[[+include file="footer.tpl"]]