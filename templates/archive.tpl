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
				<h4 class="modal-title">Flagg for: Hello world.txt</h4>
			</div>
			<div class="modal-body">
				<div class="panel-group" id="accordion">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
									Godkjenning
									<div class="model-flags-collaps-info">
										<i class="fa fa-question" title="Stemme ikke avgitt."></i>
										<div class="progress">
											<div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
												20%
											</div>
										</div>
									</div>
								</a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse">
							<div class="panel-body">
								<p>Denne fila er åpen for godkjenning. Dersom fila hører til på YouKok gjør du en god gjerning ved å stemme for å godkjenne den, slik at andre kan dra nytte av den seinere.</p>
								<p>Om fila skulle stride mot våre <a href="#">retningslinjer</a> kan du enten stemme for å avvise fila, eller, i store overtrap av reglementet, velge å <a href="#">rapportere</a> den.</p>
								<hr />
								<p>1 av 5 godkjenninger. Du har <em>ikke</em> avgitt din stemme.</p>
								<button type="button" class="btn btn-primary">Godkjenn</button> <button type="button" class="btn btn-danger">Avvis</button>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
									Endring av navn
								</a>
							</h4>
						</div>
						<div id="collapseTwo" class="panel-collapse collapse">
							<div class="panel-body">
								<p>Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</p>
							</div>
						</div>
					</div>
				</div>
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
    <li><a href="#" id="archive-context-info">Vis info</a></li>
    
    <li class="divider"></li>

    <li><a href="#" id="archive-context-flags">Vis flagg <span class="badge" id="archive-context-menu-flags">0</span></a></li>
    <li><a href="#" id="archive-context-report">Rapporter</a></li>
    
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