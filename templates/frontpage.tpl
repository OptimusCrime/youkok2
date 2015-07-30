[[+include file="header.tpl"]]
            <div class="row" id="frontpage-hello">
                <div class="col-xs-12">
                    <h1>Hei og velkommen til Youkok2</h1>
                    <h3>Den beste kokeboka på nettet!</h3>
                </div>
            </div>
            [[+if $USER_IS_LOGGED_IN == FALSE]]<div class="row" id="frontpage-wellholder">
                <div class="well">
                    <div class="row">
                        <div class="col-xs-12 col-sm-9" id="frontpage-info">
                            <p>Vi har for tiden <b>[[+$HOME_STATS_USERS]]</b> registrerte brukere, <b>[[+$HOME_STATS_FILES]]</b> filer og totalt <b>[[+$HOME_STATS_DOWNLOADS]]</b> nedlastninger i vårt system.</p>
                            <p><em>Nettsiden er helt åpen og krever ikke at du registrerer deg for å kunne bruke den.</em></p>
                            <p>Som anonym bruker får du mulighet til å laste opp filer og poste nyttige linker, men disse må godkjennes manuelt av en administrator. Alle bidrag er velkomne, så lenge de ikke strider mot våre <a href="retningslinjer">retningslinjer</a>.</p>
                            <p>Om du velger å registrere deg får mulighet til å laste opp filer, poste linker og opprette mapper uten at dette må forhåndsgodkjennes. Du får også muligheten til å lagre favoritter og får en oversikt over dine siste nedlastninger på forsiden.</p>
                            <p>- Youkok2</p>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                            <div id="frontpage-links">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-12">
                                        <span class="elm-md-bigger"><a class="login-opener" data-toggle="dropdown" href="logg-inn">Logg inn</a><br /></span>
                                        <span class="elm-md-smaller"><a href="logg-inn">Logg inn</a><br /></span>
                                        <a href="registrer">Registrer</a><br />
                                        <a href="glemt-passord">Glemt passord</a><br />
                                    </div>
                                    <div class="col-xs-6 col-sm-12" id="frontpage-links-second">
                                        <a href="om">Om</a><br />
                                        <a href="retningslinjer">Retningslinjer</a><br />
                                        <a href="hjelp">Hjelp</a><br />
                                        <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>[[+/if]]

            <div class="row">
                <div class="col-xs-12 col-sm-6">
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
                                <a href="logg-inn" class="elm-md-smaller">Logg inn</a>
                                <a href="logg-inn" data-toggle="dropdown" class="login-opener elm-md-bigger">Logg inn</a> eller
                                <a href="registrer">registrer deg</a>.
                            </em>
                        </li>[[+/if]]

                    </ul>
                </div>
                <div class="col-xs-12 col-sm-6">
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
                                <a href="logg-inn" class="elm-md-smaller">Logg inn</a>
                                <a href="logg-inn" data-toggle="dropdown" class="login-opener elm-md-bigger">Logg inn</a> eller
                                <a href="registrer">registrer deg</a>.
                            </em>
                        </li>[[+/if]]

                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="list-header">
                        <h2>Nyeste elementer</h2>
                    </div>
                    <ul class="list-group">
                    [[+if count($HOME_NEWEST) == 0]]    <li class="list-group-item"><em>Det er visst ingen nedlastninger her</em></li>[[+else]][[+foreach $HOME_NEWEST as $element]]    <li class="list-group-item">
                            <a rel="nofollow" target="_blank" href="[[+$element->getFullUrl()]]">
                                [[+$element->getName()]]
                            </a>[[+if $element->hasParent()]] @ [[+if $element->getParent(true)->hasParent()]]

                            <a href="[[+$element->getParent(true)->getFullUrl()]]">[[+$element->getParent(true)->getName()]]</a>, [[+/if]]

                            <a href="[[+$element->getRootParent()->getFullUrl()]]" title="[[+$element->getRootParent()->getCourseName()]]" data-placement="top" data-toggle="tooltip">[[+$element->getRootParent()->getCourseCode()]]</a>[[+/if]]

                            [<span class="moment-timestamp help" data-toggle="tooltip" title="[[+$element->getAdded(true)]]" data-ts="[[+$element->getAdded(true)]]">Laster...</span>]
                        </li>
                    [[+/foreach]]
 [[+/if]]</ul>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="list-header">
                        <h2 class="can-i-be-inline">Mest populære</h2>
                        <div class="btn-group" id="frontpage-most-popular-dropdown">
                            <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                            <span id="home-most-popular-selected">
                                [[+if $USER_MOST_POPULAR_DELTA == 0]]Denne uka[[+else if $USER_MOST_POPULAR_DELTA == 1]]Denne måneden[[+else if $USER_MOST_POPULAR_DELTA == 2]]Dette året[[+else if $USER_MOST_POPULAR_DELTA == 4]]I dag[[+else]]Alltid[[+/if]]

                            </span>
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" id="home-most-popular-dropdown">
                                <li[[+if $USER_MOST_POPULAR_DELTA == 4]] class="disabled"[[+/if]]><a data-delta="4" href="#">I dag</a></li>
                                <li[[+if $USER_MOST_POPULAR_DELTA == 0]] class="disabled"[[+/if]]><a data-delta="0" href="#">Denne uka</a></li>
                                <li[[+if $USER_MOST_POPULAR_DELTA == 1]] class="disabled"[[+/if]]><a data-delta="1" href="#">Denne måneden</a></li>
                                <li[[+if $USER_MOST_POPULAR_DELTA == 2]] class="disabled"[[+/if]]><a data-delta="2" href="#">Dette året</a></li>
                                <li[[+if $USER_MOST_POPULAR_DELTA == 3]] class="disabled"[[+/if]]><a data-delta="3" href="#">Alltid</a></li>
                            </ul>
                        </div>
                    </div>
                    <ul class="list-group" id="home-most-popular">
                    [[+if count($HOME_MOST_POPULAR) == 0]]    <li class="list-group-item"><em>Det er visst ingen nedlastninger her</em></li>[[+else]][[+foreach $HOME_MOST_POPULAR as $element]]    <li class="list-group-item">
                            <a rel="nofollow" target="_blank" href="[[+$element->getFullUrl()]]">
                                [[+$element->getName()]]
                            </a>[[+if $element->hasParent()]] @ [[+if $element->getParent(true)->hasParent()]]

                            <a href="[[+$element->getParent(true)->getFullUrl()]]">[[+$element->getParent(true)->getName()]]</a>, [[+/if]]

                            <a href="[[+$element->getRootParent()->getFullUrl()]]" title="[[+$element->getRootParent()->getCourseName()]]" data-placement="top" data-toggle="tooltip">[[+$element->getRootParent()->getCourseCode()]]</a>[[+/if]]

                            [[[+$element->getDownLoadCount($USER_MOST_POPULAR_DELTA)]]]
                        </li>
                    [[+/foreach]]
[[+/if]]</ul>
                </div>
            </div>
[[+include file="footer.tpl"]]
</body>
</html>