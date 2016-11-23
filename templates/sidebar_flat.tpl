                    <div class="sidebar-element">
                        <h3>Les også</h3>
                        <ul class="list-group">
                            <li class="list-group-item[[+if $VIEW_NAME == 'about']] selected[[+/if]]"><a href="[[+path_for name="about"]]">Om YouKok2</a></li>
                            <li class="list-group-item[[+if $VIEW_NAME == 'terms']] selected[[+/if]]"><a href="[[+path_for name="terms"]]">Retningslinjer</a></li>
                            <li class="list-group-item[[+if $VIEW_NAME == 'help']] selected[[+/if]]"><a href="[[+path_for name="help"]]">Hjelp</a></li>
                            <li class="list-group-item"><a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt</a></li>
                        </ul>
                    </div>
