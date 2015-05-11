        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12" id="footer">
            <p>
                <a href="changelog.txt"><span class="first">Youkok2 v[[+$VERSION]]</span></a>::
                <span><a href="om">Om Youkok2</a></span>::
                <span><a href="retningslinjer">Retningslinjer</a></span>::
                <span><a href="hjelp">Hjelp</a></span>::
                <span><a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt</a></span>::
                <span class="last">[[+nocache]][[+$TIMER]][[+/nocache]]</span>
            </p>
            [[+nocache]]
                [[+if $DEV]]
                    <p>
                        <span>ElementCollection: [[+$DEV_ELEMENT_COLLECTION_NUM]]</span> ::
                        <span>Antall queries: [[+$DEV_QUERIES_NUM]]</span> ::
                        <span>Antall fetches: [[+$DEV_CACHE_LOAD_NUM]]</span> ::
                        <span><a id="toggle-queries" href="#"><span class="nopadd">Vis</span> alle queries</a></span> ::
                        <span class="last"><a id="toggle-cache-loads" href="#"><span class="nopadd">Vis</span> alle cache loads</a></span>
                    </p>
                    
                    <div id="queries">
                        [[+$DEV_QUERIES_BACKTRACE]]
                    </div>
                    
                    <div id="cache-load">
                        [[+$DEV_CACHE_LOAD_BACKTRACE]]
                    </div>
                [[+/if]]
            [[+/nocache]]
        </div>
        
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.0/moment.min.js"></script>
<script type="text/javascript" src="//code.highcharts.com/4.0.4/highcharts.js"></script>
[[+nocache]][[+if $DEV]]<script type="text/javascript" src="assets/js/libs/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="assets/js/libs/jquery.fileupload.js"></script>
<script type="text/javascript" src="assets/js/libs/jquery.ba-outside-events.min.js"></script>
<script type="text/javascript" src="assets/js/libs/jquery.countdown.min.js"></script>
<script type="text/javascript" src="assets/js/youkok.js?v=[[+$VERSION]]"></script>
<script type="text/javascript" src="assets/js/youkok.admin.js?v=[[+$VERSION]]"></script>[[+else]]<script type="text/javascript" src="assets/js/youkok.min.js"></script>[[+/if]]
[[+/nocache]][[+if $SITE_USE_GA == true]]
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-50619069-1', 'youkok2.com');
ga('send', 'pageview');
</script>[[+/if]]
</body>
</html>