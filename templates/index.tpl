[[+include file="header.tpl"]]

[[+if $HOME_INFOBOX != '']]
    <div class="row" id="frontpage-wellholder">
        <div class="col-md-9">
            <div class="well">
                [[+$HOME_INFOBOX]]
            </div>
        </div>
        <div class="col-md-3">
            <div class="well">
                <h3>Kjappe linker</h3>
                <a class="login-opener" data-toggle="dropdown" href="logg-inn">Logg inn</a><br />
                <a href="registrer">Registrer</a><br />
                <a href="glemt-passord">Glemt passord</a><br />
                <br />
                <a href="om">Om</a><br />
                <a href="retningslinjer">Retningslinjer</a><br />
                <a href="hjelp">Hjelp</a><br />
                <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt</a>
            </div>
        </div>
    </div>
[[+/if]]

<div class="row">
    <div class="col-md-6">
        <div class="list-header">
            <h2>Mine favoritter</h2>
        </div>
        <ul class="list-group" id="favorites-list">
            [[+$HOME_USER_FAVORITES]]
        </ul>
    </div>
    <div class="col-md-6">
        <div class="list-header">
            <h2>Mine siste nedlastninger</h2>
        </div>
        <ul class="list-group">
            [[+$HOME_USER_LATEST]]
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="list-header">
            <h2>Nyeste filer</h2>
        </div>
        <ul class="list-group">
            [[+$HOME_NEWEST]]
        </ul>
    </div>
    <div class="col-md-6">
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
                    <li[[+if $HOME_MOST_POPULAR_DELTA == -1]] class="disabled"[[+/if]]><a data-delta="4" href="#">I dag</a></li>
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