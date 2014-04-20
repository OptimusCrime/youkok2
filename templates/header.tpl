<!DOCTYPE html>
<html>
<head>
    <base href="[[+$SITE_URL_FULL]]" />
    <title>Youkok2.net :: Den beste kokeboka</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta content="IE=Edge" http-equiv="X-UA-Compatible" />
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script language="javascript" type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.0/moment.min.js"></script>
    <script type="text/javascript" src="assets/js/lib/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/lib/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="assets/js/lib/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="assets/js/lib/jquery.fileupload.js"></script>
    <script type="text/javascript" src="assets/js/youkok.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/lib/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/lib/bootstrap-theme.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/lib/font-awesome.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/youkok.css" />
</head>
<body>
<input type="hidden" name="search-base" id="search-base" value="[[+$SITE_SEARCH_BASE]]" />
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="[[+$SITE_RELATIVE]]">Youkok2.net</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li[[+if $HEADER_MENU == 'HOME']] class="active"[[+/if]]><a href="[[+$SITE_RELATIVE]]">Hjem</a></li>
                <li[[+if $HEADER_MENU == 'ARCHIVE']] class="active"[[+/if]]><a href="kokebok/">Kokebok</a></li>
                <li[[+if $HEADER_MENU == 'WOS']] class="active"[[+/if]]><a href="wall-of-shame">Wall of Shame</a></li>
                <li[[+if $HEADER_MENU == 'ABOUT']] class="active"[[+/if]]><a href="om">Om Youkok2</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                [[+if $BASE_USER_IS_LOGGED_IN == true]]
                    <li>
                        <a href="#" data-toggle="dropdown">Min bruker <span title="Din karma: [[+$BASE_USER_KARMA]]" class="badge">[[+$BASE_USER_KARMA]]</span> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
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
                        <a href="#" data-toggle="dropdown">Logg inn<b class="caret"></b></a>
                        <ul class="dropdown-menu" id="login-dropdown">
                            <li>
                                <form role="form" action="" method="post">
                                    <div class="form-group">
                                        <label for="login-email">E-post</label>
                                        <input type="email" name="login-email" class="form-control" id="login-email" value="" placeholder="E-post" />
                                        <label for="login-pw">Passord</label>
                                        <input type="password" name="login-pw" class="form-control" id="login-pw" value="" placeholder="Passord" />
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="login-remember" id="login-remember" value="pizza" /> Husk meg
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-default">Logg inn</button> eller <a href="registrer">Registrer deg</a>.
                                </form>
                            </li>
                        </ul>
                    </li>
                [[+/if]]
            </ul>
            <form class="navbar-form navbar-right" role="form">
                <div class="form-group div-relative" id="prefetch">
                    <input type="text" placeholder="Søk etter fag" class="form-control typeahead" id="search" />
                    <button class="btn" type="button" id="nav-search">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>  
</div>
<div class="container" id="main">
    [[+if $SITE_MESSAGES != '']]
        <div class="row">
            <div class="col-md-12">
                [[+$SITE_MESSAGES]]
            </div>
        </div>
    [[+/if]]
    <div class="row">
        <div class="col-md-12">