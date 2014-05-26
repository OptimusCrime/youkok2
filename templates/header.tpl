<!DOCTYPE html>
<html>
<head>
    <base href="[[+$SITE_URL_FULL]]" />
    [[+nocache]]
        <title>Youkok2.com :: [[+$SITE_TITLE]]</title>
    [[+/nocache]]
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="description" content="Youkok2 er den beste kokeboka for studenter på NTNU i en knipen studiehverdag." />
    <meta name="keywords" content="ntnu, kok, youkok, kokebok, øvinger, lekser, eksamen, oppgaver, fasit, trondheim" />
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.0/moment.min.js"></script>
    <script type="text/javascript" src="assets/js/lib/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/lib/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="assets/js/lib/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="assets/js/lib/jquery.fileupload.js"></script>
    <script type="text/javascript" src="assets/js/lib/jquery.ba-outside-events.min.js"></script>
    [[+nocache]]
        <script type="text/javascript" src="assets/js/youkok[[+if !$DEV]].min[[+/if]].js?v=[[+$VERSION]]"></script>
    [[+/nocache]]
    <link rel="stylesheet" type="text/css" href="assets/css/lib/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/lib/font-awesome.css" />
    [[+nocache]]
        <link rel="stylesheet" type="text/css" href="assets/css/youkok[[+if !$DEV]].min[[+/if]].css?v=[[+$VERSION]]" />
    [[+/nocache]]
</head>
<body>
[[+nocache]]
    <input type="hidden" name="cache-time" id="cache-time" value="[[+$CACHE_TIME]]" />
    <input type="hidden" name="search-base" id="search-base" value="[[+$SITE_SEARCH_BASE]]" />
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="[[+$SITE_RELATIVE]]">Youkok2.com</a>
        </div>
        [[+nocache]]
            <div class="navbar-collapse collapse">
                <ul id="main-nav" class="nav navbar-nav">
                    <li[[+if $HEADER_MENU == 'HOME']] class="active"[[+/if]]><a href="[[+$SITE_RELATIVE]]">Hjem</a></li>
                    <li[[+if $HEADER_MENU == 'ARCHIVE']] class="active"[[+/if]]><a href="kokeboka/">Kokeboka</a></li>
                    <li class="hidden[[+if $HEADER_MENU == 'SEARCH']] active[[+/if]]"><a href="sok">Søk</a></li>
                    <li[[+if $HEADER_MENU == 'ABOUT']] class="active"[[+/if]]><a href="om">Om Youkok2</a></li>
                    <li[[+if $HEADER_MENU == 'HELP']] class="active"[[+/if]]><a href="hjelp">Hjelp</a></li>
                    [[+if $BASE_USER_IS_LOGGED_IN == true]]
                        <li class="hidden"><a href="profil/innstillinger">Min bruker</a></li>
                        <li class="hidden"><a href="logg-ut">Logg ut</a></li>
                    [[+else]]
                        <li class="hidden"><a href="logg-inn">Logg inn</a></li>
                    [[+/if]]
                </ul>
                <ul class="nav navbar-nav navbar-right" id="navbar-dropdown-outer">
                    [[+if $BASE_USER_IS_LOGGED_IN == true]]
                        <li>
                            <a href="#" data-toggle="dropdown">Min bruker <span title="Din karma: [[+$BASE_USER_KARMA]]" class="badge">[[+$BASE_USER_KARMA]]</span> <b class="caret"></b></a>
                            <ul class="dropdown-menu" id="user-dropdown">
                                <li role="presentation" class="dropdown-header">[[+$BASE_USER_NICK]]</li>
                                <li class="divider"></li>
                                <li><a href="profil/innstillinger">Innstillinger</a></li>
                                <li><a href="profil/historikk">Historikk</a></li>
                                <li class="divider"></li>
                                <li><a href="logg-ut">Logg ut</a></li>
                            </ul>
                        </li>
                    [[+else]]
                        <li>
                            <a id="dropdown-menu-opener" href="#" data-toggle="dropdown">Logg inn <b class="caret"></b></a>
                            <ul class="dropdown-menu" id="login-dropdown">
                                <li>
                                    <form role="form" action="" method="post">
                                        <div class="form-group">
                                            <label for="login-email">E-post</label>
                                            <input type="email" name="login-email" class="form-control" id="login-email" value="" placeholder="" />
                                            <label for="login-pw">Passord</label>
                                            <input type="password" name="login-pw" class="form-control" id="login-pw" value="" placeholder="" />
                                        </div>
                                        <div id="login-float-container">
                                            <button type="submit" class="btn btn-primary">Logg inn</button>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="login-remember" id="login-remember" value="pizza" /> Husk meg
                                                </label>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <hr />
                                        <a role="button" href="registrer" class="btn btn-default">Registrer</a>
                                        <a role="button" href="glemt-passord" class="btn btn-default">Glemt passord</a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    [[+/if]]
                </ul>
                <form class="navbar-form navbar-right" id="search-form" name="search-form" role="form" action="sok" method="get">
                    <div class="form-group div-relative" id="prefetch">
                        <input type="text" placeholder="Søk etter fag" class="form-control typeahead" id="s" name="s" />
                        <button class="btn" type="button" id="nav-search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        [[+/nocache]]
    </div>  
</div>
<div class="container" id="main">
    <div class="row">
        <div class="col-md-12" id="main_messages">
            [[+nocache]]
                [[+if $SITE_MESSAGES != '']]
                    [[+$SITE_MESSAGES]]
                [[+/if]]
            [[+/nocache]]
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">