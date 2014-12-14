        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12" id="footer">
            <p>
                <a href="changelog.txt"><span class="first">Youkok2 v[[+$VERSION]]</span></a>::
                <span><a href="om">Om Youkok2</a></span>::
                <span><a href="retningslinjer">Retningslinjer</a></span>::
                <span><a href="privacy">Privacy og så videre</a></span>::
                <span><a href="hjelp">Hjelp</a></span>::
                <span><a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt</a></span>::
                <span class="last">[[+$TIMER]]</span>
            </p>
            [[+nocache]]
                [[+if $DEV]]
                    <p>
                        <span>ElementCollection: [[+$DEV_ELEMENT_COLLECTION]]</span> ::
                        <span>Antall queries: [[+$DEV_QUERIES_NUM]]</span> ::
                        <span>Antall fetches: [[+$DEV_CACHE_LOAD]]</span> ::
                        <span class="last"><a id="toggle-queries" href="#"><span class="nopadd">Vis</span> alle queries</a></span>
                    </p>
                    
                    <div id="queries">
                        [[+$DEV_QUERIES]]
                    </div>
                [[+/if]]
            [[+/nocache]]
        </div>
        
    </div>
</div>
[[+if $SITE_USE_GA == true]]
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-50619069-1', 'youkok2.com');
    ga('send', 'pageview');
    </script>
[[+/if]]
</body>
</html>