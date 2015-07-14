[[+include file="header.tpl"]]
            <div class="row">
                <div id="archive-top" class="col-xs-12 col-md-8">
                    <ol class="breadcrumb" id="archive-breadcrumbs">
                        <li><a href="[[+$SITE_URL]]">Hjem</a></li>
                        <li><a href="emner/">Emner</a></li>[[+foreach $ARCHIVE_ELEMENT_PARENTS as $element]]

                        <li>[[+if $element->getId() == $ARCHIVE_ELEMENT->getId()]][[+if !$ARCHIVE_ELEMENT->hasParent()]][[+$ARCHIVE_ELEMENT->getCourseCode()]][[+else]][[+$element->getName()]][[+/if]][[+else]]<a href="[[+$element->getFullUrl($ROUTE_ARCHIVE)]]">[[+if !$element->hasParent()]][[+$element->getCourseCode()]][[+else]][[+$element->getName()]][[+/if]]</a>[[+/if]]</li>[[+/foreach]]

                    </ol>
                    [[+nocache]]<div id="archive-title">
                        [[+if !$ARCHIVE_ELEMENT->hasParent()]]<h1>[[+$ARCHIVE_ELEMENT->getCourseCode()]]</h1>
                        <span>&mdash;</span>
                        <h2>[[+$ARCHIVE_ELEMENT->getCourseName()]]</h2>[[+else]]<h1>[[+$ARCHIVE_ELEMENT->getName()]]</h1>[[+/if]]
                        [[+if $USER_IS_LOGGED_IN]]

                        <i class="fa fa-star archive-heading-star-TODO" data-archive-id="[[+$ARCHIVE_ELEMENT->getId()]]" id="archive-heading-star"></i>[[+/if]]

                    </div>[[+/nocache]][[+if $ARCHIVE_EMPTY === true]]

[[+include file="archive_empty.tpl"]][[+else]]

[[+include file="archive_content.tpl"]][[+/if]]

                </div>
                <div id="sidebar" class="col-xs-12 col-md-4 sidebar-no-top-margin">[[+include file="archive_sidebar.tpl"]]
                </div>
            </div>
[[+include file="footer.tpl"]]