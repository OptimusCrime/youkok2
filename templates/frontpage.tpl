[[+include file="header.tpl"]]
            <div class="row" id="frontpage-hello">
                <div class="col-xs-12">
                    <h1>Velkommen til Youkok2</h1>
                    <h3>Den beste kokeboka på nettet!</h3>
                </div>
            </div>
            <div class="row" id="frontpage-wellholder">
                <div class="well">
                    <div class="row">
                        <div class="col-xs-12 col-sm-9" id="frontpage-info">
                            <p>Vi har for tiden <b>[[+$FRONTPAGE_INFO_USERS]]</b> registrerte brukere, <b>[[+$FRONTPAGE_INFO_FILES]]</b> filer og totalt <b>[[+$FRONTPAGE_INFO_DOWNLOADS]]</b> nedlastninger i vårt system.</p>
                            <p><em>Nettsiden er helt åpen og krever ikke at du registrerer deg for å kunne bruke den.</em></p>
                            <p>Som anonym bruker får du mulighet til å laste opp filer og poste nyttige linker, men disse må godkjennes manuelt av en administrator. Alle bidrag er velkomne, så lenge de ikke strider mot våre <a href="[[+path_for name="terms"]]">retningslinjer</a>.</p>
                            <p>Om du velger å registrere deg får mulighet til å laste opp filer, poste linker og opprette mapper uten at dette må forhåndsgodkjennes. Du får også muligheten til å lagre favoritter og får en oversikt over dine siste nedlastninger på forsiden.</p>
                            <p>- Youkok2</p>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                            <div id="frontpage-links">

                            </div>
                        </div>
                     </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6 frontpage-box">
                    <div class="list-header">
                        <h2>Mine favoritter</h2>
                    </div>
                    <ul class="list-group" id="favorites-list">[[+if $USER_IS_LOGGED_IN == true]][[+if count($HOME_USER_FAVORITES) == 0]]

                        <li class="list-group-item"><em>Du har ingen favoritter</em></li>[[+else]][[+foreach $HOME_USER_FAVORITES as $element]]

                        <li class="list-group-item">
                            <a[[+if !$element->isDirectory()]] target="_blank"[[+/if]] href="[[+$element->getFullUrl()]]">[[+if !$element->hasParent()]]<strong>[[+$element->getCourseCode()]]</strong> &mdash; [[+$element->getCourseName()]][[+else]][[+$element->getName()]][[+/if]]</a>
                            <i title="Fjern favoritt" data-id="[[+$element->getId()]]" class="fa fa-times-circle star-remove"></i>
                        </li>[[+/foreach]]
                        [[+/if]]
                    [[+else]]

                        <li class="list-group-item">
                            <em>

                            </em>
                        </li>[[+/if]]

                    </ul>
                </div>
                <div class="col-xs-12 col-sm-6 frontpage-box">
                    <div class="list-header">
                        <h2>Mine siste nedlastninger</h2>
                    </div>
                    <ul class="list-group">[[+if $USER_IS_LOGGED_IN == true]][[+if count($HOME_USER_LATEST) == 0]]

                        <li class="list-group-item"><em>Du har ingen nedlastninger</em></li>[[+else]][[+foreach $HOME_USER_LATEST as $element]]

                        <li class="list-group-item">
                            <a rel="nofollow" target="_blank" href="[[+$element->getFullUrl()]]">
                                [[+$element->getName()]]
                            </a>[[+if $element->hasParent()]] @ [[+if $element->getParent(true)->hasParent()]]

                            <a href="[[+$element->getParent(true)->getFullUrl()]]">[[+$element->getParent(true)->getName()]]</a>, [[+/if]]

                            <a href="[[+$element->getRootParent()->getFullUrl()]]" title="[[+$element->getRootParent()->getCourseName()]]" data-placement="top" data-toggle="tooltip">[[+$element->getRootParent()->getCourseCode()]]</a>[[+/if]]

                        </li>[[+/foreach]]
                        [[+/if]]
                    [[+else]]

                        <li class="list-group-item">
                            <em>

                            </em>
                        </li>[[+/if]]

                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 frontpage-box">
                    <div class="list-header">
                        <h2>Nyeste elementer</h2>
                    </div>
                    <ul class="list-group">
                    [[+if count($FRONTPAGE_LATEST) == 0]]    <li class="list-group-item"><em>Det er visst ingen nedlastninger her</em></li>[[+else]][[+foreach $FRONTPAGE_LATEST as $element]]    <li class="list-group-item">
                            <a rel="nofollow" target="_blank" href="[[+$element->getFullUrl()]]">
                                [[+$element->getName()]]
                            </a>[[+if $element->hasParent()]] @ [[+if $element->getParent(true)->hasParent()]]

                            <a href="[[+$element->getParent(true)->getFullUrl()]]">[[+$element->getParent(true)->getName()]]</a>, [[+/if]]

                            <a href="[[+$element->getRootParent()->getFullUrl()]]" title="[[+$element->getRootParent()->getCourseName()]]" data-placement="top" data-toggle="tooltip">[[+$element->getRootParent()->getCourseCode()]]</a>[[+/if]]

                            [<span class="moment-timestamp help" data-toggle="tooltip" title="[[+$element->getAdded(true)]]" data-ts="[[+$element->getAdded()]]">Laster...</span>]
                        </li>
                    [[+/foreach]]
 [[+/if]]</ul>
                </div>
                <div class="col-xs-12 col-sm-6 frontpage-box frontpage-module" data-id="1" data-variable="module1_delta">
                    <div class="list-header">
                        <h2 class="can-i-be-inline">Mest populære</h2>
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                            <span class="most-popular-label">
                                [[+if $USER_MOST_POPULAR_ELEMENT == 1]]I dag[[+else if $USER_MOST_POPULAR_ELEMENT == 2]]Denne uka[[+else if $USER_MOST_POPULAR_ELEMENT == 3]]Dette måneden[[+else if $USER_MOST_POPULAR_ELEMENT == 4]]Dette året[[+else]]Alltid[[+/if]]

                            </span>
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu home-most-popular-dropdown">
                                <li[[+if $USER_MOST_POPULAR_ELEMENT == 1]] class="disabled"[[+/if]]><a data-delta="1" href="#">I dag</a></li>
                                <li[[+if $USER_MOST_POPULAR_ELEMENT == 2]] class="disabled"[[+/if]]><a data-delta="2" href="#">Denne uka</a></li>
                                <li[[+if $USER_MOST_POPULAR_ELEMENT == 3]] class="disabled"[[+/if]]><a data-delta="3" href="#">Denne måneden</a></li>
                                <li[[+if $USER_MOST_POPULAR_ELEMENT == 4]] class="disabled"[[+/if]]><a data-delta="4" href="#">Dette året</a></li>
                                <li[[+if $USER_MOST_POPULAR_ELEMENT == 0]] class="disabled"[[+/if]]><a data-delta="0" href="#">Alltid</a></li>
                            </ul>
                        </div>
                    </div>
                    <ul class="list-group">
                    [[+if count($HOME_MOST_POPULAR_ELEMENTS) == 0]]    <li class="list-group-item"><em>Det er visst ingen nedlastninger her</em></li>[[+else]][[+foreach $HOME_MOST_POPULAR_ELEMENTS as $element]]    <li class="list-group-item">
                            <a rel="nofollow" target="_blank" href="[[+$element->getFullUrl()]]">
                                [[+$element->getName()]]
                            </a>[[+if $element->hasParent()]] @ [[+if $element->getParent(true)->hasParent()]]

                            <a href="[[+$element->getParent(true)->getFullUrl()]]">[[+$element->getParent(true)->getName()]]</a>, [[+/if]]

                            <a href="[[+$element->getRootParent()->getFullUrl()]]" title="[[+$element->getRootParent()->getCourseName()]]" data-placement="top" data-toggle="tooltip">[[+$element->getRootParent()->getCourseCode()]]</a>[[+/if]]

                            [[[+$element->getDownLoadCount($USER_MOST_POPULAR_ELEMENT)]]]
                        </li>
                    [[+/foreach]]
[[+/if]]</ul>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 frontpage-box frontpage-module" data-id="2" data-variable="module2_delta">
                    <div class="list-header">
                        <h2 class="can-i-be-inline">Mest populære fag</h2>
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                            <span class="most-popular-label">
                                [[+if $USER_MOST_POPULAR_COURSES == 1]]I dag[[+else if $USER_MOST_POPULAR_COURSES == 2]]Denne uka[[+else if $USER_MOST_POPULAR_COURSES == 3]]Dette måneden[[+else if $USER_MOST_POPULAR_COURSES == 4]]Dette året[[+else]]Alltid[[+/if]]

                            </span>
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu home-most-popular-dropdown">
                                <li[[+if $USER_MOST_POPULAR_COURSES == 1]] class="disabled"[[+/if]]><a data-delta="1" href="#">I dag</a></li>
                                <li[[+if $USER_MOST_POPULAR_COURSES == 2]] class="disabled"[[+/if]]><a data-delta="2" href="#">Denne uka</a></li>
                                <li[[+if $USER_MOST_POPULAR_COURSES == 3]] class="disabled"[[+/if]]><a data-delta="3" href="#">Denne måneden</a></li>
                                <li[[+if $USER_MOST_POPULAR_COURSES == 4]] class="disabled"[[+/if]]><a data-delta="4" href="#">Dette året</a></li>
                                <li[[+if $USER_MOST_POPULAR_COURSES == 0]] class="disabled"[[+/if]]><a data-delta="0" href="#">Alltid</a></li>
                            </ul>
                        </div>
                    </div>
                    <ul class="list-group">
                    [[+if count($HOME_MOST_POPULAR_COURSES) == 0]]    <li class="list-group-item"><em>Det er visst ingen fag her</em></li>[[+else]][[+foreach $HOME_MOST_POPULAR_COURSES as $element]]    <li class="list-group-item">
                            <a rel="nofollow" href="[[+$element->getFullUrl()]]">
                                <strong>[[+$element->getCourseCode()]]</strong> &mdash; [[+$element->getCourseName()]]
                            </a> [~[[+$element->getDownLoadCount($USER_MOST_POPULAR_COURSES)]]]
                        </li>
                    [[+/foreach]]
[[+/if]]</ul>
                </div>
                <div class="col-xs-12 col-sm-6 frontpage-box">
                    <div class="list-header">
                        <h2>Siste besøkte fag</h2>
                    </div>
                    <ul class="list-group">
                    [[+if count($HOME_LAST_VISITED) == 0]]    <li class="list-group-item"><em>Det er visst ingen fag her</em></li>[[+else]][[+foreach $HOME_LAST_VISITED as $element]]    <li class="list-group-item">
                            <a rel="nofollow" href="[[+$element->getFullUrl()]]">
                                <strong>[[+$element->getCourseCode()]]</strong> &mdash; [[+$element->getCourseName()]]
                            </a>
                        </li>
                    [[+/foreach]]
[[+/if]]</ul>
                </div>
            </div>
[[+include file="footer.tpl"]]
<script type="text/template" class="template-frontpage-popular-elements">
    <ul class="list-group">
        <% _.each(rc.elements,function(element) { %>
            <li class="list-group-item">
                <a href="<%- element.full_url %>" target="_blank" <% if (element.url !== null) { %>title="Link til: <%- element.url %>"<% } %>>
                    <%- element.name %>
                </a> @ <% if (element.parents.length == 2) { %>
                    <a href="<%- element.parents[0].full_url %>"><%- element.parents[0].name %></a>,
                <% } %>
                <a title="<% if (element.parents.length == 2) { %><%- element.parents[1].course_name %><% } else { %><%- element.parents[0].course_name %><% } %>" data-placement="top" data-toggle="tooltip" href="<% if (element.parents.length == 2) { %><%- element.parents[1].full_url %><% } else { %><%- element.parents[0].full_url %><% } %>"><% if (element.parents.length == 2) { %><%- element.parents[1].course_code %><% } else { %><%- element.parents[0].course_code %><% } %></a>
                [<% if (element.download_count !== null) { %><%- element.download_count %><% } else { %>0<% } %>]
            </li>
        <% }); %>
    </ul>
</script>
<script type="text/template" class="template-frontpage-no-popular-elements">
    <ul class="list-group">
        <li class="list-group-item">
            <em>Det er visst ingen nedlastninger her</em>
        </li>
    </ul>
</script>
<script type="text/template" class="template-frontpage-popular-courses">
    <ul class="list-group">
        <% _.each(rc.elements,function(element) { %>
            <li class="list-group-item">
                <a href="<%- element.full_url %>">
                    <strong><%- element.course_code %></strong> &mdash; <%- element.course_name %>
                </a> [~<%- element.download_count %>]
            </li>
        <% }); %>
    </ul>
</script>
<script type="text/template" class="template-frontpage-no-popular-courses">
    <ul class="list-group">
        <li class="list-group-item">
            <em>Det er visst ingen fag her</em>
        </li>
    </ul>
</script>
</body>
</html>
