[[+include file="header.tpl"]]

<div class="modal fade" id="modal-admin-script">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Kjører script...</h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
            </div>
        </div>
    </div>
</div>

<div class="col-md-8">
    <h1>Admin!</h1>
    
    <p><strong>[[+$ADMIN_USERS]]</strong> registrerte brukere, <strong>[[+$ADMIN_FILES]]</strong> filer i systemet og <strong>[[+$ADMIN_DOWNLOADS]]</strong> nedlastninger hvor av <strong>[[+$ADMIN_DOWNLOADS_LAST_24]]</strong> av disse er de siste 24 timene.</p>
    <p>De <strong>[[+$ADMIN_FILES]]</strong> filene utgjør totalt <strong>[[+$ADMIN_SIZE]]</strong>. De totalt <strong>[[+$ADMIN_DOWNLOADS]]</strong> nedlastningene utgjør <strong>[[+$ADMIN_BANDWIDTH]]</strong> bandwidth forbruk.</p>
    
    <h2>Scripts</h2>
    
    <ul id="admin-scripts">
        <li><a href="#" data-script="cleaccache">Clear cache</a></li>
        <li><a href="#" data-script="checkfiles">Check files 404</a></li>
        <li><a href="#" data-script="loadcourses">Load courses</a></li>
        <li><a href="#" data-script="updateimages">Update images</a></li>
    </ul>
    
    <h2>Nedlastninger</h2>
    <div class="admin-graph" id="admin-graph">
        <div class="admin-graph-hidden">
            [[+$ADMIN_GRAPH_DATA]]
        </div>
        <div id="admin-graph1-display"></div>
    </div>
    
    <h2>Nedlastninger acc</h2>
    <div class="admin-graph" id="admin-graph-acc">
        <div class="admin-graph-hidden">
            [[+$ADMIN_GRAPH_DATA_ACC]]
        </div>
        <div id="admin-graph2-display"></div>
    </div>
    
    <h2>Tall</h2>
    <ul>
        <li>Antall fag: [[+$ADMIN_NUM_COURSES]]</li>
        <li>Antall filer: [[+$ADMIN_NUM_FILES]]</li>
        <li>Antall linker: [[+$ADMIN_NUM_LINKS]]</li>
        <li>Antall mapper: [[+$ADMIN_NUM_DIRS]]</li>
    </ul>
    
</div>
<div class="col-md-4">
    <div id="archive-sidebar-readalso" class="archive-sidebar">
        <h3>Nedlastninger pr. dag</h3>
        <ul>
            [[+$ADMIN_DOWNLOADS_PR_DAY]]
        </ul>
    </div>
</div>

<script type="text/javascript" src="assets/js/youkok.admin.js" /></script>

[[+include file="footer.tpl"]]