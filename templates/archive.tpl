[[+include file="header.tpl"]]

<div class="col-md-8">
	<ol class="breadcrumb" id="archive-breadcrumbs">
		<li><a href="/">Hjem</a></li>
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