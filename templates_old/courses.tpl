[[+include file="header.tpl"]]
        <div class="row">
            <div class="col-xs-12 col-md-8">
                <div class="row">
                    <div class="col-xs-12">
                        <ol class="breadcrumb" id="archive-breadcrumbs">
                            <li><a href="[[+$SITE_URL]]">Hjem</a></li>
                            <li class="active">Emner</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 center">
                        <h1>Emner</h1>
                    </div>
                </div>
                <div class="row">[[+foreach $COURSE_COLLECTION as $SUB_COLLECTION]]

                    <div class="col-xs-12 col-md-6 course-box">
                        <h3>[[+$SUB_COLLECTION.letter]]</h3>
                        <ul class="list-group">[[+foreach $SUB_COLLECTION.courses as $element]]

                            <li class="[[+if $element->empty]]course-empty [[+/if]]list-group-item">
                                <a href="[[+path_for
                                    name="archive"
                                    data=["params" => "[[+$element->fullUri]]" ]
                                ]]"><strong>[[+$element->courseCode]]</strong> &mdash; [[+$element->courseName]]</a>
                            </li>[[+/foreach]]

                        </ul>
                    </div>[[+/foreach]]

                </div>
            </div>
            <div id="sidebar" class="col-xs-12 col-md-4 sidebar-no-top-margin">
                <div class="sidebar-element">
                    <div class="sidebar-element-inner">
                        <p>Fagene står alfabetisk sortert etter fagkode.</p>
                        <p>Du kan også bruke søkefeltet for å søke etter fagkoder og fagnavn.</p>
                        <p>Dersom du savner et fag i listen kan du <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">kontakte oss</a>, så legger vi det til.</p>
                    </div>
                </div>
[[+include file="sidebar/general.tpl"]]
            </div>
        </div>
[[+include file="footer.tpl"]]
[[+include file="sidebar/templates.tpl"]]
</body>
</html>