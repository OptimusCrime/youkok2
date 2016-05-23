<!DOCTYPE html>
<html>
<head>
    <base href="[[+$SITE_URL]]" />
    <title>Youkok2.com :: [[+$SITE_TITLE]]</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="icon" type="image/png" href="favicon.png" />
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="description" content="[[+if isset($SITE_DESCRPTION)]][[+$SITE_DESCRPTION]][[+else]]Youkok2 er den beste kokeboka for studenter på NTNU i en knipen studiehverdag.[[+/if]]" />
    <meta name="keywords" content="ntnu, kok, youkok, kokebok, øvinger, lekser, eksamen, oppgaver, fasit, trondheim" />
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" />
    [[+nocache]][[+if !$COMPRESS_ASSETS]]<link rel="stylesheet" type="text/css" href="assets/css/libs/bootstrap.lumen.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/youkok.css?v=[[+$VERSION]]" />[[+else]]<link rel="stylesheet" type="text/css" href="assets/css/youkok.min.css" />[[+/if]]
    
    [[+if $USER_IS_ADMIN]]<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjpbLG7YxoVW1zeiEFAEluUhE5tqcEQV0"></script>[[+/if]]
    
    <script type="text/javascript">var SITE_DATA = "[[+$SITE_DATA]]";</script>[[+/nocache]]
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
            <a class="navbar-brand" href="[[+$SITE_URL]]">Youkok2.com</a>
        </div>[[+nocache]]
        <div class="navbar-collapse collapse">
            <ul id="main-nav" class="nav navbar-nav">
                <li[[+if $HEADER_MENU == 'HOME']] class="active"[[+/if]]><a href="[[+$SITE_URL]]">Hjem</a></li>
                <li[[+if $HEADER_MENU == 'ARCHIVE']] class="active"[[+/if]]><a href="[[+TemplateHelper::urlFor('courses')]]">Emner</a></li>
                <li class="hidden[[+if $HEADER_MENU == 'SEARCH']] active[[+/if]]"><a href="[[+TemplateHelper::urlFor('search')]]">Søk</a></li>
                <li[[+if $HEADER_MENU == 'ABOUT']] class="active"[[+/if]]><a href="[[+TemplateHelper::urlFor('flat_about')]]">Om Youkok2</a></li>
                <li[[+if $HEADER_MENU == 'HELP']] class="active"[[+/if]]><a href="[[+TemplateHelper::urlFor('flat_help')]]">Hjelp</a></li>
                [[+if $USER_IS_LOGGED_IN == true]]<li class="hidden"><a href="[[+TemplateHelper::urlFor('profile_settings')]]">Min bruker</a></li>
                <li class="hidden"><a href="logg-ut">Logg ut</a></li>[[+else]]<li class="hidden"><a href="[[+TemplateHelper::urlFor('auth_login')]]">Logg inn</a></li>[[+/if]]

            </ul>
            <ul class="nav navbar-nav navbar-right" id="navbar-dropdown-outer">[[+if $USER_IS_LOGGED_IN == true]]

                <li>
                    <a href="#" data-toggle="dropdown">Min bruker <span class="badge"><span title="Din karma: [[+$USER_KARMA]]">[[+$USER_KARMA]]</span>[[+if $USER_KARMA_PENDING != 0]] <span title="Din pending karma: [[+$USER_KARMA_PENDING]]"> /  [[+$USER_KARMA_PENDING]]</span>[[+/if]]</span> <b class="caret"></b></a>
                    <ul class="dropdown-menu" id="user-dropdown">
                        <li role="presentation" class="dropdown-header">[[+$USER_NICK]]</li>
                        <li class="divider"></li>
                        <li><a href="[[+TemplateHelper::urlFor('profile_settings')]]">Innstillinger</a></li>
                        <li><a href="[[+TemplateHelper::urlFor('profile_history')]]">Karma / Historikk</a></li>
                        [[+if $USER_IS_ADMIN == true]]<li><a href="[[+TemplateHelper::urlFor('admin_home')]]">Admin</a></li>[[+/if]]
                        
                        <li class="divider"></li>
                        <li><a href="[[+TemplateHelper::urlFor('auth_logout')]]?_token=[[+$CSRF_TOKEN]]">Logg ut</a></li>
                    </ul>
                </li>[[+else]]

                <li>
                    <a id="dropdown-menu-opener" href="#" data-toggle="dropdown">Logg inn <b class="caret"></b></a>
                    <ul class="dropdown-menu" id="login-dropdown">
                        <li>
                            <form action="[[+TemplateHelper::urlFor('auth_login')]]" method="post">
                                <input type="hidden" name="_token" value="[[+$CSRF_TOKEN]]" />
                                <div class="form-group">
                                    <label for="login-email">E-post</label>
                                    <input type="email" name="login-email" class="form-control" id="login-email" value="[[+if isset($LOGIN_EMAIL)]][[+$LOGIN_EMAIL]][[+/if]]" placeholder="" />
                                    <label for="login-pw">Passord</label>
                                    <input type="password" name="login-pw" class="form-control" id="login-pw" value="" placeholder="" />
                                </div>
                                <div id="login-float-container">
                                    <button type="submit" class="btn btn-primary">Logg inn</button>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="login-remember" id="login-remember" value="remember" /> Husk meg
                                        </label>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <hr />
                                <a role="button" href="[[+TemplateHelper::urlFor('auth_register')]]" class="btn btn-default">Registrer</a>
                                <a role="button" href="[[+TemplateHelper::urlFor('auth_forgotten_password')]]" class="btn btn-default">Glemt passord</a>
                            </form>
                        </li>
                    </ul>
                </li>[[+/if]]

            </ul>
            <form class="navbar-form navbar-right" id="search-form" name="search-form" action="[[+TemplateHelper::urlFor('search')]]" method="get">
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
