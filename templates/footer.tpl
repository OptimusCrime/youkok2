    	</div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12" id="footer">
        	<p>
        		<a href="changelog.txt"><span class="first">Youkok2 v[[+$VERSION]]</span></a> ::
        		<span><a href="om">Om Youkok2</a></span> ::
        		<span><a href="retningslinjer">Retningslinjer</a></span> ::
                <span><a href="privacy">Privacy og så videre</a></span> ::
                <span><a href="wall-of-shame">Wall of Shame</a></span> ::
                <span><a href="hjelp">Hjelp</a></span> ::
                <span class="last"><a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt</a></span>
        	</p>
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
[[+nocache]]
    [[+if DEV]]
        <div id="dev">
            <p><b>Parse time:</b> [[+$DEV_TIME]]</p>
            <p><b>Antall queries:</b> [[+$DEV_QUERIES]]</p>
        </div>
    [[+/if]]
[[+/nocache]]
</body>
</html>