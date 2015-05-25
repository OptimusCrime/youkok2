[[+include file="header.tpl"]]

<div class="row">
    <div class="col-xs-12 col-md-8">
        <h1>Søk</h1>
        <form class="" id="search-form2" name="search-form" role="form" action="sok" method="get">
            <div class="form-group div-relative" id="prefetch2">
                <input type="text" placeholder="Søk etter fag" class="form-control typeahead" value="[[+$SEARCH_QUERY]]" id="s2" name="s" />
                <button class="btn" type="button" id="nav-search2">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </form>
        [[+if $SEARCH_MODE == 'search']]
            <p>Ditt søk på "<strong>[[+$SEARCH_QUERY]]</strong>" returnerte <strong>[[+$SEARCH_NUM]]</strong> treff.</p>
            <p>Søket vil kun treffe på fagkoder og fagnavn. Dersom et fag mangler i listen kan du <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">kontakte oss</a>, så legger vi det til.</p>
            <hr />
            <div class="row">
                <div class="col-xs-12 col-md-6 course-box">
                    <ul class="list-group">
                        [[+$SEARCH_RESULT]]
                    </ul>
                </div>
            </div>
         [[+else]]
            <p>Søk i feltet ovenfor på enten fagkoder eller fagnavn.</p>
            <p>Søket vil kun treffe på fagkoder og fagnavn. Dersom et fag mangler i listen kan du <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">kontakte oss</a>, så legger vi det til.</p>
         [[+/if]]
    </div>
    <div class="col-xs-12 col-md-4">
        [[+include file="sidebar.tpl"]]
    </div>
</div>

[[+include file="footer.tpl"]]