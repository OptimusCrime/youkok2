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
                <li class="header">Navigajon</li>
                <li class="active treeview">
                    <a href="#">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="index.html"><i class="fa fa-circle-o"></i> Dashboard v1</a>
                        </li>
                        <li class="active">
                            <a href="index2.html"><i class="fa fa-circle-o"></i> Dashboard v2</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-share"></i> <span>Multilevel</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="#"><i class="fa fa-circle-o"></i> Level One</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-circle-o"></i> Level One <i class="fa fa-angle-left pull-right"></i></a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="#"><i class="fa fa-circle-o"></i> Level Two</a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-circle-o"></i> Level Two <i class="fa fa-angle-left pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="#"><i class="fa fa-circle-o"></i> Level Three</a>
                                        </li>
                                        <li>
                                            <a href="#"><i class="fa fa-circle-o"></i> Level Three</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-circle-o"></i> Level One</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-files-o"></i>
                        <span>Layout Options</span>
                        <span class="label label-primary pull-right">4</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="pages/layout/top-nav.html"><i class="fa fa-circle-o"></i> Top Navigation</a>
                        </li>
                    </ul>
                </li>
                <li class="header">
                    LABELS
                </li>
                <li>
                    <a href="#"><i class="fa fa-circle-o text-red"></i> <span>Important</span></a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Warning</span></a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Information</span></a>
                </li>
            </ul>
        </section>
    </aside>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Youkok2 Dashboard
            </h1>
            <ol class="breadcrumb">
                <li>
                    <a href="[[+$SITE_URL]]">Youkok2</a>
                </li>
                <li class="active">Dashboard</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-download"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Nedlastninger</span>
                            <span class="info-box-number">50,158</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Brukere</span>
                            <span class="info-box-number">352</span>
                        </div>
                    </div>
                </div>
                <div class="clearfix visible-sm-block"></div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-files-o"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Filer</span>
                            <span class="info-box-number">499</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-graduation-cap"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Fag</span>
                            <span class="info-box-number">3,802</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Nedlastninger de siste 30 dagene</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-center">
                                        <strong>Sales: 1 Jan, 2014 - 30 Jul, 2014</strong>
                                    </p>
                                    <div class="chart">
                                        <canvas id="salesChart" style="height: 180px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="assets/js/libs/jquery-2.1.4.min.js"></script>
<script src="assets/js/libs/bootstrap-3.3.5.min.js"></script>
<script src="assets/js/admin/youkok.admin.js"></script>
</body>
</html>