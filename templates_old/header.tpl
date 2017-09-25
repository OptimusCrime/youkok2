<!DOCTYPE html>
<html>
<head>
    <base href="[[+base_url]]" />
    <title>[[+$SITE_TITLE]] :: Youkok2.com</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="icon" type="image/png" href="favicon.png" />
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="description" content="[[+if isset($SITE_DESCRIPTION)]][[+$SITE_DESCRIPTION]][[+else]]Youkok2 er den beste kokeboka for studenter på NTNU i en knipen studiehverdag.[[+/if]]" />
    <meta name="keywords" content="ntnu, kok, youkok, kokebok, øvinger, lekser, eksamen, oppgaver, fasit, trondheim" />
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/libs/bootstrap.lumen.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/youkok.css" />
    [[+nocache]]<script type="text/javascript">var SITE_DATA = "[[+$SITE_DATA]]";</script>[[+/nocache]]
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="[[+base_url]]">Youkok2.com</a>
        </div>[[+nocache]]
        <div class="navbar-collapse collapse">
            <ul id="main-nav" class="nav navbar-nav">
                <li[[+if $HEADER_MENU == 'home']] class="active"[[+/if]]><a href="[[+base_url]]">Hjem</a></li>
                <li[[+if $HEADER_MENU == 'courses']] class="active"[[+/if]]><a href="[[+path_for name="courses"]]">Emner</a></li>
                <li class="hidden[[+if $HEADER_MENU == 'search']] active[[+/if]]"><a href="[[+path_for name="search"]]">Søk</a></li>
                <li[[+if $HEADER_MENU == 'about']] class="active"[[+/if]]><a href="[[+path_for name="about"]]">Om Youkok2</a></li>
                <li[[+if $HEADER_MENU == 'help']] class="active"[[+/if]]><a href="[[+path_for name="help"]]">Hjelp</a></li>
            </ul>
            <form class="navbar-form navbar-right" id="search-form" name="search-form" action="[[+path_for name="search"]]" method="get">
                <div class="form-group div-relative" id="prefetch">
                    <input type="text" placeholder="Søk etter fag" class="form-control typeahead" value="[[+$SEARCH_QUERY]]" id="s" name="s" />
                    <button class="btn" type="button" id="nav-search">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
        </div>[[+/nocache]]
    </div>
</div>
<div class="container" id="main">
    <div class="row">[[+nocache]][[+if count($SITE_MESSAGES) > 0]]
        [[+foreach $SITE_MESSAGES as $message]]

        <div class="alert alert-[[+$message->getType()]]">
            <p>[[+$message->getMessage()]]</p>
            <div class="alert-close">
                <i class="fa fa-times"></i>
            </div>
        </div>[[+/foreach]]
        [[+/if]][[+/nocache]]

        <div class="col-xs-12">
