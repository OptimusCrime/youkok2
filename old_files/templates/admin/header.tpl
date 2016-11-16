<!DOCTYPE html>
<html>
<head>
    <base href="[[+$SITE_URL]]" />
    <title>Youkok2.com :: [[+$SITE_TITLE]]</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="icon" type="image/png" href="favicon.png" />
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/libs/bootstrap.lumen.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/admin/AdminLTE.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/admin/youkok2.css" />
    [[+nocache]]<script type="text/javascript">var SITE_DATA = "[[+$SITE_DATA]]";</script>[[+/nocache]]
</head>
<body class="skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <a href="[[+$SITE_URL]]" class="logo">
            <span class="logo-mini">Yk2</span>
            <span class="logo-lg">Youkok2</span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
        </nav>
    </header>
    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                [[+foreach $ADMIN_SIDEBAR_MENU as $menu_item]]
                    <li class="[[+if $menu_item.active]]active [[+/if]]treeview">
                        <a href="[[+$menu_item.url]]">
                            <i class="fa fa-[[+$menu_item.icon]]"></i> <span>[[+$menu_item.text]]</span>
                            [[+if array_key_exists('extra', $menu_item)]][[+$menu_item.extra]][[+/if]]
                        </a>
                    </li>
                [[+/foreach]]
            </ul>
        </section>
    </aside>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Youkok2 Dashboard &mdash; [[+$ADMIN_HEADING]]
            </h1>
            <ol class="breadcrumb">
                <li>
                    <a href="[[+$SITE_URL]]">Youkok2</a>
                </li>
                <li class="active">Forside</li>
            </ol>
        </section>