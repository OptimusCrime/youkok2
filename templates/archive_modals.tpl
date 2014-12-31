<div class="modal fade" id="modal-info">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Laster...</h4>
            </div>
            <div class="modal-body" id="info-panel">
                <p>Laster...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
            </div>
        </div>
    </div>
</div>
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
                            <li><a href="#">Denne fila er min</a></li>
                            <li><a href="#">Støtende, fornærmende eller hatefulle ytringer</a></li>
                            <li><a href="#">Denne fila inneholder virus, malware og lignende</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Andre grunner</a></li>
                        </ul>
                    </div>
                    <hr />
                    <label for="model-report-text">Kommentar</label>
                    <textarea class="form-control" id="model-report-text" name="model-report-text"></textarea>
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
    <li id="archive-context-info"><a href="#">Detaljer</a></li>


    <li class="divider"></li>

    <li class="dropdown-submenu" id="archive-context-newflag-outer"><a href="#" id="archive-context-newflag">Nytt flagg</a>
        <ul class="dropdown-menu">
            <li><a href="#" id="archive-context-new-flag-name">Endre navn</a></li>
            <li style="display: none;"><a href="#" id="archive-context-new-flag-delete">Sletting av fil</a></li>
            <li style="display: none;"><a href="#" id="archive-context-new-flag-move">Flytting av fil</a></li>
        </ul>
    </li>
    <li><a href="#" id="archive-context-flags">Vis flagg <span class="badge" id="archive-context-menu-flags">0</span></a></li>
    <li><a href="#" id="archive-context-report">Rapporter</a></li>

    <li class="divider"></li>

    <li><a href="#" id="archive-context-close">Lukk</a></li>
</ul>