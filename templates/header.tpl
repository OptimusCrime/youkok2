<!DOCTYPE html>
<html>
<head>
    <base href="http://dev.optimuscrime.net/youkok2/" />
    <title>Youkok.net</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta content="IE=Edge" http-equiv="X-UA-Compatible" />
    <script type="text/javascript" src="assets/js/lib/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/lib/jquery-ui.js"></script>
    <script type="text/javascript" src="assets/js/lib/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/lib/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/lib/bootstrap-theme.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/youkok.css" />
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/youkok2/">YouKok2.net</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li[[+if $HEADER_MENU == 'HOME']] class="active"[[+/if]]><a href="/youkok2/">Hjem</a></li>
                <li[[+if $HEADER_MENU == 'ARCHIVE']] class="active"[[+/if]]><a href="arkiv/">Fag &amp; filer</a></li>
                <li[[+if $HEADER_MENU == 'WOS']] class="active"[[+/if]]><a href="wall-of-shame">WoS</a></li>
                <li[[+if $HEADER_MENU == 'ABOUT']] class="active"[[+/if]]><a href="om">Om</a></li>
                <li[[+if $HEADER_MENU == 'CONTACTS']] class="active"[[+/if]]><a href="kontakt">Kontakt</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                [[+if $BASE_USER_IS_LOGGED_IN == true]]
                    <li>
                        <a href="#" data-toggle="dropdown">Min bruker<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li role="presentation" class="dropdown-header">[[+$BASE_USER_NICK]]</li>
                            <li class="divider"></li>
                            <li><a href="profil/innstillinger">Innstillinger</a></li>
                            <li><a href="logout">Logg ut</a></li>
                        </ul>
                    </li>
                [[+else]]
                    <li>
                        <a href="#" data-toggle="dropdown">Logg inn<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>TODO</li>
                        </ul>
                    </li>
                [[+/if]]
            </ul>
            <form class="navbar-form navbar-right" role="form">
                <div class="form-group div-relative">
                    <input type="text" placeholder="Søk etter fag" class="form-control" id="search" />
                    <button class="btn" type="button" id="nav-search">
                        <i class="icon-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>  
</div>
<div class="container" id="main">
    <div class="row">
        <div class="col-md-12">