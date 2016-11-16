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
[[+/nocache]]