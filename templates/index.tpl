[[+include file="header.tpl"]]

<div class="row" id="frontpage-hello">
    <div class="col-xs-12">
        <h1>Hei og velkommen til Youkok2</h1>
        <h3>Den beste kokeboka på nettet!</h3>
    </div>
</div>

[[+if $BASE_USER_IS_LOGGED_IN == FALSE]]
    <div class="row" id="frontpage-wellholder">
        <div class="well">
            <div class="row">
                <div class="col-xs-12 col-sm-9" id="frontpage-info">
                    [[+$HOME_INFOBOX]]
                    <p>Nettsiden er helt åpen og krever ikke at du registrerer deg for å kunne bruke den.</p>
                    <p>Om du velger å registrere deg får mulighet til å lagre favoritter, se sine siste nedlastninger, 
                    samt muligheten til å laste opp filer og å bidra til å gjøre Youkok2 enda bedre. Du kan lese mer om 
                    dette i <a href="om">om-seksjonen</a> vår.</p><p>La oss gjøre studiehverdagen enklere, sammen!</p>
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
    </div>
[[+/if]]

<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="list-header">
            <h2>Mine favoritter</h2>
        </div>
        <ul class="list-group" id="favorites-list">
            [[+if $BASE_USER_IS_LOGGED_IN == true]]
                [[+$HOME_USER_FAVORITES]]
            [[+else]]
                <li class="list-group-item">
                    <em><a href="logg-inn" class="elm-md-smaller">Logg inn</a><a href="logg-inn" data-toggle="dropdown" class="login-opener elm-md-bigger">Logg inn</a> eller <a href="registrer">registrer deg</a>.</em>
                </li>
            [[+/if]]
        </ul>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="list-header">
            <h2>Mine siste nedlastninger</h2>
        </div>
        <ul class="list-group">
            [[+if $BASE_USER_IS_LOGGED_IN == true]]
                [[+$HOME_USER_LATEST]]
            [[+else]]
                <li class="list-group-item">
                    <em><a href="logg-inn" class="elm-md-smaller">Logg inn</a><a href="logg-inn" data-toggle="dropdown" class="login-opener elm-md-bigger">Logg inn</a> eller <a href="registrer">registrer deg</a>.</em>
                </li>
            [[+/if]]
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="list-header">
            <h2>Nyeste elementer</h2>
        </div>
        <ul class="list-group">
            [[+$HOME_NEWEST]]
        </ul>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="list-header">
            <h2 class="can-i-be-inline">Mest populære</h2>
            <div class="btn-group">
                <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                <span id="home-most-popular-selected">[[+if $HOME_MOST_POPULAR_DELTA == 0]]
                    Denne uka
                [[+else if $HOME_MOST_POPULAR_DELTA == 1]]
                    Denne måneden
                [[+else if $HOME_MOST_POPULAR_DELTA == 2]]
                    Dette året
                [[+else if $HOME_MOST_POPULAR_DELTA == 4]]
                    I dag
                [[+else]]
                    Alltid
                [[+/if]]</span> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" id="home-most-popular-dropdown">
                    <li[[+if $HOME_MOST_POPULAR_DELTA == 4]] class="disabled"[[+/if]]><a data-delta="4" href="#">I dag</a></li>
                    <li[[+if $HOME_MOST_POPULAR_DELTA == 0]] class="disabled"[[+/if]]><a data-delta="0" href="#">Denne uka</a></li>
                    <li[[+if $HOME_MOST_POPULAR_DELTA == 1]] class="disabled"[[+/if]]><a data-delta="1" href="#">Denne måneden</a></li>
                    <li[[+if $HOME_MOST_POPULAR_DELTA == 2]] class="disabled"[[+/if]]><a data-delta="2" href="#">Dette året</a></li>
                    <li[[+if $HOME_MOST_POPULAR_DELTA == 3]] class="disabled"[[+/if]]><a data-delta="3" href="#">Alltid</a></li>
                </ul>
            </div>
        </div>
        <ul class="list-group" id="home-most-popular">
            [[+$HOME_MOST_POPULAR]]
        </ul>
    </div>
</div>

[[+include file="footer.tpl"]]