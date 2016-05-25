                    <div class="sidebar-element">
                        <h3>Les også</h3>
                        <ul class="list-group">
                            <li class="list-group-item[[+if $BASE_QUERY == 'om']] selected[[+/if]]"><a href="[[+TemplateHelper::urlFor('flat_about')]]">Om YouKok2</a></li>
                            <li class="list-group-item[[+if $BASE_QUERY == 'retningslinjer']] selected[[+/if]]"><a href="[[+TemplateHelper::urlFor('flat_terms')]]">Retningslinjer</a></li>
                            <li class="list-group-item[[+if $BASE_QUERY == 'hjelp']] selected[[+/if]]"><a href="[[+TemplateHelper::urlFor('flat_help')]]">Hjelp</a></li>
                            <li class="list-group-item"><a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt</a></li>
                        </ul>
                    </div>
