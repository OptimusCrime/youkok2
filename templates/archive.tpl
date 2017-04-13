[[+include file="header.tpl"]]
            <div class="row">
                <div class="col-xs-12 col-md-8">
                    <div class="row">
                        <div class="col-xs-12">
                            <ol class="breadcrumb" id="archive-breadcrumbs">
                                <li><a href="[[+base_url]]">Hjem</a></li>
                                <li><a href="[[+path_for name="courses"]]">Emner</a></li>
                                [[+foreach $ARCHIVE_PARENTS as $parent]]
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
                                    </li>
                                [[+/foreach]]
                            </ol>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 archive-title">
                            <h1>
                                [[+$ARCHIVE_TITLE]]
                            </h1>
                            [[+if $ARCHIVE_SUB_TITLE != null]]
                                <h2>[[+$ARCHIVE_SUB_TITLE]]</h2>
                            [[+/if]]

                            <i class="fa fa-star archive-heading-star-TODO" data-archive-id="[[+$ARCHIVE_ID]]" id="archive-heading-star"></i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            [[+if count($ARCHIVE_CHILDREN) === 0]]
                                <div id="archive-empty" class="well">
                                    <p>Huffa!</p>
                                    <p>Det er visst ingen filer her. Du kan bidra ved å laste opp filer i panelet til høyre. Pass på at du leser våre <a href="[[+path_for name="terms"]]">retningslinjer</a> før du eventuelt gjør dette.</p>
                                    <p>Det er visst ingen filer her. Dessverre ser det også ut som om du er bannet i systemet vårt, så det betyr at du dessverre ikke kan gjøre noe med dette heller.</p>
                                    <p>Det er visst ingen filer her. Du kan gjøre noe med dette ved å <a href="registrer">registrere deg</a> og bidra på Youkok2. Dersom du allerede er registrert kan du logge inn i menyen ovenfor.</p>

                                    <p>- YouKok2</p>
                                </div>
                            [[+else]]
                                <div class="archive-list">
                                    [[+foreach $ARCHIVE_CHILDREN as $child]]
                                        <div class="archive-row">
                                            <div class="archive-row-icon">
                                                <div style="background-image: url('assets/images/icons/[[+$child->icon]]');"></div>
                                            </div>
                                            <div class="archive-row-name">
                                                <span>
                                                    <a href="[[+element_url element=[[+$child]]]]"[[+if $child->link !== null]] target="_blank" title="Link til: [[+$child->link]]"[[+/if]]>
                                                        [[+$child->name]]
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="archive-row-downloads">
                                                <span>
                                                    2,761
                                                </span>
                                            </div>
                                            <div class="archive-row-age">
                                                <span>
                                                    1. april 2017
                                                </span>
                                            </div>
                                        </div>
                                    [[+/foreach]]
                                </div>
                            [[+/if]]
                        </div>
                    </div>
                </div>
                <div id="sidebar" class="col-xs-12 col-md-4 sidebar-no-top-margin">
                    [[*+include file="archive_sidebar.tpl"*]]
                </div>
            </div>
[[+include file="footer.tpl"]]
[[*+include file="xsidebar_templates.tpl"*]]
[[*+include file="xsidebar_archive.tpl"*]]
</body>
</html>
