[[+nocache]]
<div id="archive-controlls" class="sidebar-element">
    <h3>Kontroller</h3>
    <div class="sidebar-element-inner">
        [[+if $ARCHIVE_USER_CAN_CONTRIBUTE == true]]
        <div id="archive-create-controlls">
            <button type="button" id="archive-create-file" class="btn btn-default">Last opp fil</button>
            &nbsp;
            <button type="button" class="btn btn-default" id="archive-create-folder">Opprett mappe</button>
            &nbsp;
            <button type="button" class="btn btn-default" id="archive-create-link">Opprett link</button>
        </div>

        <div id="archive-create-folder-div">
            <form role="form" action="" id="archive-create-folder-form" method="post">
                <div class="form-group">
                    <label for="archive-create-folder-name"><strong>Name</strong></label>
                    <input type="text" name="archive-create-folder-name" class="form-control" id="archive-create-folder-name" value="" placeholder="Navn på mappen du ønsker å opprette" />
                </div>
                <button id="archive-create-folder-form-submit" type="submit" class="btn btn-default">Lagre</button> eller <a href="#">avbryt</a>.
            </form>
        </div>

        <div id="archive-create-file-div">
            <form role="form" action="" id="archive-create-file-form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <p><strong>Velg filer</strong></p>
                    <div id="fileupload-files">
                        <div id="fileupload-files-inner"></div>
                        <div class="fileupload-file fileupload-add">
                            <p>Filer opplastet</p>
                                        <span class="btn btn-default fileinput-button">
                                            <span>Legg til filer</span>
                                            <input type="file" name="files[]" multiple>
                                        </span>
                        </div>
                    </div>
                    <div id="progress">
                        <div class="bar" style="width: 0%;"></div>
                    </div>
                </div>
                <p><a href="retningslinjer" target="_blank">Se liste over godkjente filtyper</a>.</p>
                <button id="archive-create-file-form-submit" type="submit" class="btn btn-default">Last opp</button> eller <a href="#">avbryt</a>.
            </form>
        </div>

        <div id="archive-create-link-div">
            <form role="form" action="" id="archive-create-link-form" method="post">
                <div class="form-group">
                    <label for="archive-create-link-url"><strong>URL</strong></label>
                    <input type="text" name="archive-create-link-url" class="form-control" id="archive-create-link-url" value="" placeholder="URL for linken" />
                    <label for="archive-create-link-name"><strong>Name</strong></label>
                    <input type="text" name="archive-create-link-name" class="form-control" id="archive-create-link-name" value="" placeholder="Alternativt navn" />
                </div>
                <button id="archive-create-link-form-submit" type="submit" class="btn btn-default">Lagre</button> eller <a href="#">avbryt</a>.
            </form>
        </div>
        [[+else]]
        [[+if $ARCHIVE_USER_ONLINE == true]]
        [[+if $ARCHIVE_USER_BANNED == true]]
        <p>Du er bannet fra systemet og kan dermed ikke bidra på Youkok2 lenger.</p>
        [[+elseif $ARCHIVE_USER_HAS_KARMA == false]]
        <p>Du har <strong>0</strong> i karma. På grunn av dette kan du ikke lenger bidra på Youok2.</p>
        [[+/if]]
        [[+else]]
        <p>Logg inn for å kunne bidra på Youkok2.</p>
        [[+/if]]
        [[+/if]]
    </div>
</div>
[[+/nocache]]