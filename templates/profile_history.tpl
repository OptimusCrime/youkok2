[[+include file="header.tpl"]]
            <div class="row">
                <div class="col-xs-12 col-md-8">
                    <h1>Karma / Historikk</h1>
                    <p>Tabellen under viser hvordan du har opparbeidet deg din karma. Rader i grønt betyr at ditt bidrag har blitt godkjent av systemet mens en rød rad betyr at et bidrag har blitt avvist eller fjernet.</p>
                    <p>Rader som er grået ut er bidrag som enda ikke er lagt til din permanente karma, fordi de fortsatt blir evaluert.</p>
                    <ul class="list-group">[[+if count($PROFILE_USER_HISTORY) == 0]]
                        
                        <li class="list-group-item">Du har ikke opparbeidet deg noe karma.</li>[[+else]][[+foreach $PROFILE_USER_HISTORY as $karma]]
                            <li class="list-group-item [[+if $karma->isPending() == 1]]list-group-item-gray[[+else]][[+if $karma->getState() == 1]]list-group-item-success[[+else]]list-group-item-danger[[+/if]][[+/if]]">
                                <div class="width33">
                                    [[+if $karma->getFile(true) == null]]Ukjent fil[[+else]]<a href="[[+$karma->getFile(true)->getParent(true)->getFullUrl()]]">[[+$karma->getFile(true)->getName()]]</a>[[+/if]]
                                </div>
                                <div class="width33">
                                    [[+if $karma->getFile(true) == null]]???[[+else]][[+if $karma->getFile(true)->isDirectory()]]Ny mappe[[+elseif $karma->getFile(true)->isLink()]]Ny link[[+else]]Ny fil[[+/if]][[+/if]]
                                </div>
                                <div class="width33">
                                    <span class="moment-timestamp" style="cursor: help;" title="[[+$karma->getAdded(true)]]" data-ts="[[+$karma->getAdded(true)]]">
                                        Laster...
                                    </span>
                                    <span class="badge">[[+if $karma->getState() == 1]]+[[+else]]-[[+/if]][[+$karma->getValue()]]</span></div>
                            </li>
                        [[+/foreach]][[+/if]]
                    </ul>
                </div>
                <div id="sidebar" class="col-xs-12 col-md-4">
[[+include file="sidebar.tpl"]]
                </div>
            </div>
[[+include file="footer.tpl"]]
[[+include file="sidebar_templates.tpl"]]
</body>
</html>