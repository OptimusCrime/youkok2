[[+include file="header.tpl"]]
[[+include file="archive_modals.tpl"]]
[[+nocache]]
<input type="hidden" value="[[+$ARCHIVE_ID]]" id="archive-id" name="archive-id" />
<input type="hidden" value="[[+$ARCHIVE_USER_ONLINE]]" id="archive-online" name="archive-online" />
<input type="hidden" value="[[+$ARCHIVE_USER_CAN_CONTRIBUTE]]" id="archive-can-c" name="archive-can-c" />
<div id="archive_accepted_filetypes">[[+$ACCEPTED_FILETYPES]]</div>
<div id="archive_accepted_fileendings">[[+$ACCEPTED_FILEENDINGS]]</div>
[[+/nocache]]
<div class="col-md-8" id="archive-top">
    <ol class="breadcrumb" id="archive-breadcrumbs">
        <li><a href="[[+$SITE_RELATIVE]]">Hjem</a></li>
        <li><a href="emner/">Emner</a></li>
        [[+$ARCHIVE_BREADCRUMBS]]
    </ol>
    [[+nocache]]
    <div id="archive-title">
        [[+$ARCHIVE_TITLE]]
        <a id="archive-zip" href="[[+$ARCHIVE_ZIP_DOWNLOAD]]">Last ned som .zip ([[+$ARCHIVE_ZIP_DOWNLOAD_NUM]])</a>
    </div>
    [[+/nocache]]

    [[+if $ARCHIVE_EMPTY === true]]
        [[+include file="archive_empty.tpl"]]
    [[+else]]
        [[+include file="archive_content.tpl"]]
    [[+/if]]
</div>
<div class="col-md-4" id="sidebar">
    [[+include file="archive_sidebar.tpl"]]
</div>

[[+include file="footer.tpl"]]