[[+include file="header.tpl"]]

<ul class="dropdown-menu" id="archive-context-menu">
    <li id="archive-context-menu-id" role="presentation" class="dropdown-header">Laster</li>
    <li class="divider"></li>
    <li><a href="#">Last ned<span id="archive-context-menu-size"></span></a></li>
    <li><a href="#">Vis info</a></li>
    <li class="divider"></li>
    <li><a href="#">xxx</a></li>
    <li><a href="#">xxx</a></li>
    <li class="divider"></li>
    <li><a href="#">Lukk</a></li>
</ul>

<div class="col-md-8">
	<ol class="breadcrumb" id="archive-breadcrumbs">
		<li><a href="[[+$SITE_RELATIVE]]">Hjem</a></li>
		[[+$ARCHIVE_BREADCRUMBS]]
	</ol>

	<h1>[[+$ARCHIVE_TITLE]]</h1>
	[[+if $ARCHIVE_MODE == 'browse']]
		<ul id="archive-list">
		    [[+$ARCHIVE_DISPLAY]]
		</ul>
	[[+else]]
		[[+$ARCHIVE_DISPLAY]]
	[[+/if]]
</div>
<div class="col-md-4">
	<div id="archive-history">
		<h3>Historikk</h3>
		<ul>
			<li><strong>Path</strong> opprettet i <strong>MFEL1010</strong> av <em>Anonym</em>.</li>
			<li><strong>MFEL101</strong> endret navn til <strong>MFEL1010</strong> av <em>Anonym</em>.</li>
			<li><strong>MFEL101</strong> opprettet av <em>Anonym</em>.</li>
		</ul>
	</div>
</div>

[[+include file="footer.tpl"]]