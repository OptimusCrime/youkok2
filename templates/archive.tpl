[[+include file="header.tpl"]]

<div class="modal fade" id="modal-new-flag">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Laster...</h4>
			</div>
			<div class="modal-body">
				<div id="modal-new-flag-name" class="modal-new-flag-container">
					<p>Dersom du syntes dette elementet burde ha et nytt navn kan du foreslå det her.</p> 
					<form action="" method="post" name="modal-new-flag-name-form" id="modal-new-flag-name-form">
						<label for="modal-new-flag-name-name">Nytt navn</label>
	                    <div class="input-group">
							<input type="text" name="modal-new-flag-name-name" class="form-control" id="modal-new-flag-name-name" value="" placeholder="Skriv inn ditt forslag her" />
							<span class="input-group-addon">.laster</span>
						</div>
						<label for="modal-new-flag-name-comment">Kommentar</label>
						<textarea class="form-control" id="modal-new-flag-name-comment" name="modal-new-flag-name-comment"></textarea>
						<button type="submit" class="btn btn-default">Send</button> eller <a href="#">lukk</a>.
					</form>
				</div>

				<div id="modal-new-flag-delete" class="modal-new-flag-container">
					<p>Dersom du syntes dette elementet burde slettes kan du foreslå det her.</p> 
					<form action="" method="post" name="modal-new-flag-delete-form" id="modal-new-flag-delete-form">
						<label for="modal-new-flag-delete-comment">Kommentar</label>
						<textarea class="form-control" id="modal-new-flag-delete-comment" name="modal-new-flag-delete-comment"></textarea>
						<button type="submit" class="btn btn-default">Send</button> eller <a href="#">lukk</a>.
					</form>
				</div>

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
				<h4 class="modal-title">Flagg for:</h4>
			</div>
			<div class="modal-body">
				<div class="panel-group" id="flags-panel">
					Laster...
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
				<h4 class="modal-title">Rapporter:</h4>
			</div>
			<div class="modal-body">
				<form action="" method="post" id="modal-report-form" name="modal-report-form">
					Velg kategori:
					<div class="dropdown">
						<button class="btn dropdown-toggle" type="button" id="model-report-category" data-toggle="dropdown">
							<span id="modal-report-selected">Brudd på åndsverkloven</span>
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu" aria-labelledby="model-report-category">
							<li class="disabled"><a href="#">Brudd på åndsverkloven</a></li>
							<li><a href="#">Spam</a></li>
							<li><a href="#">Støtende, fornærmende eller hatefulle ytringer</a></li>
							<li><a href="#">Andre grunner</a></li>
						</ul>
					</div>
					<hr />
					<label for="model-report-text">Kommentar</label>
					<textarea class="form-control" id="" name=""></textarea>
					<button type="submit" class="btn btn-default">Send</button> eller <a href="#">lukk</a>.
				</form>
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
    <li id="archive-context-star"><a href="#" id="archive-context-star-inside">Legg til favoritt</a></li>
    
    <li class="divider"></li>

    <li class="dropdown-submenu" id="archive-context-newflag-outer"><a href="#" id="archive-context-newflag">Nytt flagg</a>
    	<ul class="dropdown-menu">
    		<li><a href="#" id="archive-context-new-flag-name">Endre navn</a></li>
    		<li><a href="#" id="archive-context-new-flag-delete">Sletting av <span class="archive-context-type"></span></a></li>
    		<li><a href="#" id="archive-context-new-flag-move">Flytting av <span class="archive-context-type"></span></a></li>
    	</ul>
    </li>
    <li><a href="#" id="archive-context-flags">Vis flagg <span class="badge" id="archive-context-menu-flags">0</span></a></li>
    <li><a href="#" id="archive-context-report">Rapporter</a></li>
    
    <li class="divider"></li>

    <li><a href="#" id="archive-context-close">Lukk</a></li>
</ul>

<input type="hidden" value="[[+$ARCHIVE_ID]]" id="archive-id" name="archive-id" />
<input type="hidden" value="[[+$ARCHIVE_USER_ONLINE]]" id="archive-pizza" name="archive-pizza" />

<div class="col-md-8">
	<ol class="breadcrumb" id="archive-breadcrumbs">
		<li><a href="[[+$SITE_RELATIVE]]">Hjem</a></li>
		[[+$ARCHIVE_BREADCRUMBS]]
	</ol>

	<h1>[[+$ARCHIVE_TITLE]]</h1>
	[[+if $ARCHIVE_MODE == 'browse']]
		<div id="archive_accepted_filetypes">[[+$ARCHIVE_ACCEPTED_FILETYPES]]</div>
		<ul id="archive-list">
		    [[+$ARCHIVE_DISPLAY]]
		</ul>
	[[+else]]
		[[+$ARCHIVE_DISPLAY]]
	[[+/if]]
</div>
<div class="col-md-4">
	[[+if $ARCHIVE_MODE == 'browse']]
		<div id="archive-controlls" class="archive-sidebar">
			<h3>Kontroller</h3>
			[[+if $ARCHIVE_USER_VERIFIED == true]]
				<div id="archive-create-controlls">
					<button type="button" id="archive-create-file" class="btn btn-default">Last opp fil</button> <button type="button" class="btn btn-default" id="archive-create-folder">Opprett mappe</button>
				</div>

				<div id="archive-create-folder-div">
					<form role="form" action="" id="archive-create-folder-form" method="post">
	                    <div class="form-group">
	                    	<label for="archive-create-folder-name">Navn</label>
	                        <input type="text" name="archive-create-folder-name" class="form-control" id="archive-create-folder-name" value="" placeholder="Navn på mappen du ønsker å opprette" />
	                    </div>
	                    <button type="submit" class="btn btn-default">Lagre</button> eller <a href="#">avbryt</a>.
					</form>
				</div>

				<div id="archive-create-file-div">
					<form role="form" action="" id="archive-create-file-form" method="post" enctype="multipart/form-data">
	                    <div class="form-group">
	                    	<p><strong>Velg filer</strong></p>
							<div id="fileupload-files">
								<div id="fileupload-files-inner"></div>
								<div class="fileupload-file fileupload-add">
									<p>Filer opplastet</p>
									<span class="btn btn-default fileinput-button">
										<span>Legg til filer</span>
										<input type="file" name="files[]" multiple>
									</span>
								</div>
							</div>
							<div id="progress">
								<div class="bar" style="width: 0%;"></div>
							</div>
	                    </div>
	                    <p><a href="retningslinjer" target="_blank">Se liste over godkjente filtyper</a>.</p>
	                    <button type="submit" class="btn btn-default">Last opp</button> eller <a href="#">avbryt</a>.
					</form>
				</div>
			[[+else]]
				<p>Logg inn og/eller registrer din NTNU-bruker for å kunne legge til filer og opprette mapper.</p>
			[[+/if]]
		</div>
	[[+/if]]

	[[+if $ARCHIVE_MODE == 'browse']]
		<div id="archive-help" class="archive-sidebar">
			<h3>Hjelp</h3>
			<p>Kokeboka skal være lett å bruke. Du laster ned filer ved å klikke på dem. Ønsker du å utforske en mappe trykker du enkelt på mappa.</p>
			<p>Du kan også høyreklikke på en fil eller en mappe for å få opp ytteligere valg. Her kan du favoritisere elementet, se utvidet informasjon, <a href="hjelp">rapportere</a> regelbrudd eller <a href="hjelp">flagge</a> elementet dersom du mener det er på sin plass. Les deg opp på forskjellen mellom å rapportere og å flagge før du gjør noe.</p>
			<p><a href="hjelp">Se utvidet hjelp for mer informasjon</a>.</p>
		</div>
	[[+else]]
		<div id="archive-help" class="archive-sidebar">
			<h3>Hjelp</h3>
			<p>Fagene står alfabetisk sortert etter fagkode.</p>
			<p>Du kan også bruke søkefeltet for å søke etter fagkoder og fagnavn.</p>
			<p>Dersom du savner et fag i listen kan du <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">kontakte oss</a>, så legger vi det til.</p>
		</div>
	[[+/if]]

	[[+if $ARCHIVE_MODE == 'browse']]
		<div id="archive-history" class="archive-sidebar">
			<h3>Historikk</h3>
			<div id="archive-history-inside">
				<p>Laster...</p>
			</div>
		</div>
	[[+/if]]

	<div id="archive-asd" class="archive-sidebar">
		<h3>Reklame</h3>
		<p>Herpaderp</p>
	</div>
</div>

[[+include file="footer.tpl"]]