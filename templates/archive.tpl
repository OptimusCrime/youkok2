[[+include file="header.tpl"]]
            <div class="row">
                <div id="archive-top" class="col-xs-12 col-md-8">
                    <ol class="breadcrumb" id="archive-breadcrumbs">
                        <li><a href="[[+base_url]]">Hjem</a></li>
                        <li><a href="[[+path_for name="courses"]]">Emner</a></li>[[+foreach $ARCHIVE_PARENTS as $parent]]
                        <li>
                            [[+if $parent->id != $ARCHIVE_ID]]
                                <a href="[[+path_for name="archive" data=["params" => "[[+$parent->fullUri]]"] ]]">
                            [[+/if]]
                           [[+if $parent->parent === null]]
                                [[+$parent->courseCode]]
                           [[+else]]
                                [[+$parent->name]]
                           [[+/if]]
                            [[+if $parent->id != $ARCHIVE_ID]]
                                </a>
                             [[+/if]]
                        </li>[[+/foreach]]
                    </ol>
                    <div id="archive-title">
                        <h1>
                            [[+$ARCHIVE_TITLE]]
                            [[+if $ARCHIVE_SUB_TITLE != null]]
                                <span>&mdash;</span>
                                <h2>[[+$ARCHIVE_SUB_TITLE]]</h2>
                            [[+/if]]
                        </h1>

                        <i class="fa fa-star archive-heading-star-TODO" data-archive-id="[[+$ARCHIVE_ID]]" id="archive-heading-star"></i>

                    </div>[[+if $ARCHIVE_EMPTY === true]]

[[+include file="archive_empty.tpl"]][[+else]]

[[*include file="archive_content.tpl"*]][[+/if]]

                </div>
                <div id="sidebar" class="col-xs-12 col-md-4 sidebar-no-top-margin">[[*+include file="archive_sidebar.tpl"*]]
                </div>
            </div>
[[+include file="footer.tpl"]]
[[*+include file="xsidebar_templates.tpl"*]]
[[*+include file="xsidebar_archive.tpl"*]]
</body>
</html>
