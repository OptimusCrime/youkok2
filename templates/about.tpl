[[+include file="header.tpl"]]
            <div class="row">
                <div class="col-xs-12 col-md-8">
                    <h1>Om Youkok2</h1>
                    <div class="well">
                        <p>Kok først når du har fått gryte. ~ <em>Youkok.com</em></p>
                    </div>
                    <h3>Den originale Kokeboka</h3>
                    <p>Som student ved NTNU kommer du før eller siden over begrepet å <em>koke</em>. Et av de mest populære sidene for å finne kok på nettet har i mange år vært Christians Kokebok, bedre kjent som
                    <a href="http://www.youkok.com" target="_blank">Youkok.com</a>. Denne nettsiden har vært en fantastisk ressurs for flere tusen studenter i en knipen studenthverdag.</p>
                    <h3>Problemet med de fleste kokesider</h3>
                    <p>Problemene med de fleste kokesidene har vært at de er knyttet til kun en enkelt person. Denne personen legger selv ut øvinger, eksamensoppgaver og løsningsforslag når han eller hun tar faget, men så fort personen er ferdig på NTNU stopper den å bli oppdatert. Det blir også rotete å benytte seg av en haug forskjellige ressurser, samtidig som det også er vanskelig å finne nye og gode sider.</p>
                    <h3>Youkok2 og muligheten for alle til å bidra</h3>
                    <p>Tanken vår med Youkok2 var å gjøre det mulig for alle å bidra med hva man skulle ønske. Om dette skulle være opplastninger direkte til siden, eller linker til nyttige sider og/eller quizer. Med et slikt system kan Youkok2 holde seg oppdatert og relevant i fremtiden også.</p>
                    <p>Man trenger ikke en gang en bruker på Youkok2 for å kunne laste opp filer eller poste linker, men disse må godkjennes av en administrator før de vises på siden. Om man registrerer seg blir disse synlige med en gang. Man får også mulighet til å opprette mapper under hvert enkelt fag.</p>
                    <p>Dette var en begresning vi så på som nødvendig da vi ikke ønsker å bryte med opphavsrett eller å publisere filer av dårlig kvalitet. Mer informasjon om dette kan du finne under <a href="[[+path_for name="terms"]]">retningslinjer</a>.</p>
                    <h3>Teknisk om nettsiden</h3>
                    <p>Backend er skrevet i sin helthet i PHP. Av rammeverk, snippets og annet har vi følgende:</p>
                    <ul>
                        <li><a href="http://www.php.net/manual/en/book.pdo.php" target="_blank">PHP Data Objects</a></li>
                        <li><a href="https://getcomposer.org/" target="_blank">Composer</a></li>
                        <li><a href="http://phinx.org/" target="_blank">Phinx</a></li>
                        <li><a href="http://www.smarty.net" target="_blank">Smarty</a></li>
                        <li><a href="https://github.com/Synchro/PHPMailer" target="_blank">PHPMailer</a></li>
                        <li><a href="https://phpunit.de" target="_blank">PHPUnit</a></li>
                        <li><a href="https://github.com/sebastianbergmann/php-timer/" target="_blank">PHP_Timer</a></li>
                        <li><a href="https://github.com/matthiasmullie/minify" target="_blank">Minify</a></li>
                        <li><a href="https://github.com/deceze/Kunststube-CSRFP" target="_blank">Kunststube\CSRFP</a></li>
                    </ul>
                    <ul>
                        <li><a href="http://getbootstrap.com" target="_blank">Bootstrap</a></li>
                        <li><a href="http://bootswatch.com" target="_blank">Bootswatch Lumen</a></li>
                        <li><a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">Font Awesome</a></li>
                        <li><a href="http://jquery.com" target="_blank">jQuery</a> &amp; <a href="https://jqueryui.com" target="_blank">jQuery UI</a></li>
                        <li><a href="http://momentjs.com" target="_blank">Moment.js</a></li>
                        <li><a href="http://twitter.github.io/typeahead.js/" target="_blank">Typeahead.js</a></li>
                        <li><a href="https://github.com/blueimp/jQuery-File-Upload" target="_blank">jQuery-File-Upload</a></li>
                        <li><a href="http://www.highcharts.com" target="_blank">Highcharts</a></li>
                    </ul>
                    <p>I tillegg er ikoner for filtyper og noe inspirasjon hentet fra <a href="http://www.mollify.org" target="_blank">Mollify</a>.</p>
                </div>
                <div class="col-xs-12 col-md-4" id="sidebar">
[[+include file="sidebar_flat.tpl"]]
[[+include file="sidebar.tpl"]]
                </div>
            </div>
[[+include file="footer.tpl"]]
[[+include file="sidebar_templates.tpl"]]
</body>
</html>