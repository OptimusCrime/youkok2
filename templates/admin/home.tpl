[[+include file="admin/header.tpl"]]
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Youkok2 Dashboard &mdash; Forside
            </h1>
            <ol class="breadcrumb">
                <li>
                    <a href="[[+$SITE_URL]]">Youkok2</a>
                </li>
                <li class="active">Forside</li>
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
[[+include file="admin/footer.tpl"]]