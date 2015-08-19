        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12" id="footer">
            <p>
                <a href="changelog.txt">Youkok2 v[[+$VERSION]]</a>
                [[+nocache]][[+if $DEV]]<a href="https://github.com/OptimusCrime/youkok2/commit/[[+$GIT_HASH]]" target="_blank">[[+$GIT_HASH_SHORT]]</a>[[+/if]][[+/nocache]]

                <a href="om">Om Youkok2</a>
                <a href="retningslinjer">Retningslinjer</a>
                <a href="hjelp">Hjelp</a>
                <a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt</a>
                <span>[[+nocache]][[+$TIMER]][[+/nocache]]</span>
            </p>[[+nocache]][[+if $DEV]]

            <p>
                <span>Antall queries: [[+$DEV_QUERIES_NUM]]</span>
                <span>Antall fetches: [[+$DEV_CACHE_LOAD_NUM]]</span>
                <a id="toggle-queries" href="#"><span class="nopadd">Vis</span> alle queries</a>
            </p>
            <div id="queries">
                [[+$DEV_QUERIES_BACKTRACE]]
            </div>[[+/if]][[+/nocache]]

        </div>
    </div>
</div>
[[+nocache]][[+if $OFFLINE]]<script type="text/javascript" src="assets/js/libs/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="assets/js/libs/jquery-ui-1.11.4.min.js"></script>
<script type="text/javascript" src="assets/js/libs/bootstrap-3.3.5.min.js"></script>
<script type="text/javascript" src="assets/js/libs/moment-2.10.3.min.js"></script>
<script type="text/javascript" src="assets/js/libs/underscore-1.8.3.min.js"></script>[[+else]]
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
<script type="text/javascript" src="https://code.highcharts.com/4.0.4/highcharts.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>[[+/if]]

[[+if !$COMPRESS_ASSETS]]<script type="text/javascript" src="assets/js/libs/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="assets/js/libs/jquery.fileupload.js"></script>
<script type="text/javascript" src="assets/js/libs/jquery.ba-outside-events.min.js"></script>
<script type="text/javascript" src="assets/js/libs/jquery.countdown.min.js"></script>
[[+$JS_MODULES]][[+else]]<script type="text/javascript" src="assets/js/youkok.min.js"></script>[[+/if]]
[[+/nocache]][[+if $SITE_USE_GA == true]]
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-50619069-1', 'youkok2.com');
ga('send', 'pageview');
</script>[[+/if]]
