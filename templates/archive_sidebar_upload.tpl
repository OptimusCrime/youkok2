[[+nocache]]
<div id="archive-controlls" class="sidebar-controlls sidebar-element[[+if !$BASE_USER_IS_LOGGED_IN]] archive-controlls-offline[[+/if]]">
    <div class="sidebar-element-inner">
        [[+if $ARCHIVE_USER_CAN_CONTRIBUTE or !$BASE_USER_IS_LOGGED_IN]]
            <div id="archive-create-controlls">
                <button type="button" id="archive-create-file" class="btn btn-default">Last opp fil</button>
                &nbsp;
                [[+if $ARCHIVE_USER_CAN_CONTRIBUTE]]<button type="button" class="btn btn-default" id="archive-create-folder">Opprett mappe</button>[[+/if]]
                &nbsp;
                <button type="button" class="btn btn-default" id="archive-create-link">Post link</button>
            </div>
            
            [[+if $ARCHIVE_USER_CAN_CONTRIBUTE]]
                <div id="archive-create-folder-div">
                    <form role="form" action="" id="archive-create-folder-form" method="post">
                        <div class="form-group">
                            <label for="archive-create-folder-name"><strong>Name</strong></label>
                            <input type="text" name="archive-create-folder-name" class="form-control" id="archive-create-folder-name" value="" placeholder="Navn på mappen du ønsker å opprette" />
                        </div>
                        <button id="archive-create-folder-form-submit" type="submit" disabled class="btn btn-default">Lagre</button> eller <a href="#">avbryt</a>.
                    </form>
                </div>
            [[+/if]]
            
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
                        <input type="text" name="archive-create-link-url" class="form-control" id="archive-create-link-url" value="" placeholder="Din URL her" />
                        <div id="archive-create-link-name-holder">
                            <label for="archive-create-link-name"><strong>Name</strong></label>
                            <input type="text" name="archive-create-link-name" class="form-control" id="archive-create-link-name" value="" placeholder="Alternativt navn" />
                        </div>
                    </div>
                    <button id="archive-create-link-form-submit" type="submit" disabled class="btn btn-default">Post link</button> eller <a href="#">avbryt</a>.
                </form>
            </div>
            
            [[+if !$BASE_USER_IS_LOGGED_IN]]
                <div id="archive-warning">
                    <p>Du er ikke logget inn og eventuelle bidrag vil ikke ha noen tilknyttning til deg. Disse innsendingene må manuelt godkjennes av en administrator før de vises på siden. Om du <a href="retningslinjer">registrerer deg</a> vil filene være øyeblikkelig synlig. Du får også mulighet til å opprette mapper.</p>
                </div>
            [[+/if]]
            
        [[+else]]
            [[+if $ARCHIVE_USER_BANNED]]
                <p>Du er bannet fra systemet og kan dermed ikke bidra på Youkok2 lenger.</p>
            [[+else]]
                <p>Du har <strong>0</strong> i karma. På grunn av dette kan du ikke lenger bidra på Youok2.</p>
            [[+/if]]
        [[+/if]]
    </div>
</div>
[[+/nocache]]