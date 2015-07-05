                    <ul id="archive-list">[[+foreach $ARCHIVE_CONTENT as $element]]

                        <li>
                            <div class="archive-item-dropdown">
                                <div class="archive-item-dropdown-arrow">
                                    <i class="fa fa-caret-down"></i>
                                </div>
                                <div class="archive-dropdown-content">
                                    <p>Valg</p>
                                    <ul>
                                        <li><a href="#">Info</a></li>
                                        <li class="sep"></li>
                                        <li><a href="#" class="archive-dropdown-close">Lukk</a></li>
                                    </ul>
                                </div>
                            </div>
                            <a href="[[+if $element->isLink()]][[+$element->generateUrl($ROUTE_REDIRECT)]][[+elseif $element->isLink()]][[+$element->generateUrl($ROUTE_DOWNLOAD)]][[+else]][[+$element->generateUrl($ROUTE_ARCHIVE)]][[+/if]]" [[+if !$element->isDirectory()]] target="_blank"[[+/if]][[+if $element->isLink()]] title="Link til: [[+$element->getUrl()]]"[[+/if]]>
                                <div class="archive-item">
                                    <div class="archive-badge archive-badge-right hidden">
                                        <i class="fa fa-comments-o"></i>
                                    </div>
                                    <div class="archive-item-icon" style="background-image: url('assets/images/icons/[[+if $element->isDirectory()]]folder.png[[+elseif $element->isLink()]]link.png[[+else]][[+if $element->getMissingImage()]]unknown.png[[+else]][[+$element->getMimeType()]].png[[+/if]][[+/if]]');"></div>
                                    <div class="archive-item-label">
                                        <h4>[[+$element->getName()]]</h4>
                                     </div>
                                </div>
                            </a>
                        </li>[[+/foreach]]
                    </ul>