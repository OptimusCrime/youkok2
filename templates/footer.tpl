        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12" id="footer">
            <p>
                <span><a href="changelog.txt">Youkok2 v[[+$SITE_SETTINGS.VERSION]]</a></span>
                <span><a href="[[+path_for name="about"]]">Om Youkok2</a></span>
                <span><a href="[[+path_for name="terms"]]">Retningslinjer</a></span>
                <span><a href="[[+path_for name="help"]]">Hjelp</a></span>
                <span><a href="mailto:[[+$SITE_EMAIL_CONTACT]]">Kontakt</a></span>
                <span>[[+nocache]][[+$TIMER]][[+/nocache]]</span>
                <span><a href="https://github.com/OptimusCrime/youkok2" target="_blank">GitHub</a></span>
            </p>
        </div>
    </div>
</div>
[[+if $SITE_SETTINGS.GOOGLE_ANALYTICS == true]]
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-50619069-1', 'youkok2.com');
ga('send', 'pageview');
</script>[[+/if]]
