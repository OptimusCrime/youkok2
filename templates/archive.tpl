[[+include file="header.tpl"]]
[[+include file="archive_modals.tpl"]]
[[+nocache]]
[[+/nocache]]
<div class="row">
    <div id="archive-top" class="col-xs-12 col-md-8">
        <ol class="breadcrumb" id="archive-breadcrumbs">
            <li><a href="[[+$SITE_RELATIVE]]">Hjem</a></li>
            <li><a href="emner/">Emner</a></li>
            [[+$ARCHIVE_BREADCRUMBS]]
        </ol>
        [[+nocache]]
        <div id="archive-title">
            [[+$ARCHIVE_TITLE]]
        </div>
        [[+/nocache]]

        [[+if $ARCHIVE_EMPTY === true]]
            [[+include file="archive_empty.tpl"]]
        [[+else]]
            [[+include file="archive_content.tpl"]]
        [[+/if]]
    </div>
    <div id="sidebar" class="col-xs-12 col-md-4 sidebar-no-top-margin">
        [[+include file="archive_sidebar.tpl"]]
    </div>
</div>

[[+include file="footer.tpl"]]