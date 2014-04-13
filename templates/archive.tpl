[[+include file="header.tpl"]]

<div class="modal fade" id="modal-info">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Info om: lalalalala</h4>
			</div>
			<div class="modal-body">
				<p>One fine body&hellip;</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-flags">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Flagg for: xxxx</h4>
			</div>
			<div class="modal-body">
				<p>One fine body&hellip;</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-report">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Rapporter: xxxxx</h4>
			</div>
			<div class="modal-body">
				<p>One fine body&hellip;</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
			</div>
		</div>
	</div>
</div>

<ul class="dropdown-menu" id="archive-context-menu">
    <li id="archive-context-menu-id" role="presentation" class="dropdown-header">Laster</li>
    <li class="divider"></li>

    <li id="archive-context-download"><a href="#">Last ned<span id="archive-context-menu-size"></span></a></li>
    <li id="archive-context-open"><a href="#">Åpne</a></li>
    <li><a href="#" id="archive-context-info" data-toggle="modal" data-target="#modal-info">Vis info</a></li>
    <li class="divider"></li>

    <li><a href="#" id="archive-context-flags" data-toggle="modal" data-target="#modal-flags">Vis flagg <span class="badge" id="archive-context-menu-flags">0</span></a></li>
    <li><a href="#" id="archive-context-report" data-toggle="modal" data-target="#modal-report">Rapporter</a></li>
    <li class="divider"></li>

    <li><a href="#" id="archive-context-close">Lukk</a></li>
</ul>

<div class="col-md-8">
	<ol class="breadcrumb" id="archive-breadcrumbs">
		<li><a href="[[+$SITE_RELATIVE]]">Hjem</a></li>
		[[+$ARCHIVE_BREADCRUMBS]]
	</ol>

	<h1>[[+$ARCHIVE_TITLE]] <small><i class="fa fa-star" id="archive-heading-star"></i></small></h1>
	[[+if $ARCHIVE_MODE == 'browse']]
		<ul id="archive-list">
		    [[+$ARCHIVE_DISPLAY]]
		</ul>
	[[+else]]
		[[+$ARCHIVE_DISPLAY]]
	[[+/if]]
</div>
<div class="col-md-4">
	<div id="archive-controlls" class="archive-sidebar">
		<h3>Kontroller</h3>
		<p>Herpaderp</p>
	</div>

	<div id="archive-history" class="archive-sidebar">
		<h3>Historikk</h3>
		<ul>
			<li><strong>Path</strong> opprettet i <strong>MFEL1010</strong> av <em>Anonym</em>.</li>
			<li><strong>MFEL101</strong> endret navn til <strong>MFEL1010</strong> av <em>Anonym</em>.</li>
			<li><strong>MFEL101</strong> opprettet av <em>Anonym</em>.</li>
		</ul>
	</div>

	<div id="archive-asd" class="archive-sidebar">
		<h3>Reklame</h3>
		<p>Herpaderp</p>
	</div>
</div>

[[+include file="footer.tpl"]]