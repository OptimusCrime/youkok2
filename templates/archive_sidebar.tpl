[[+include file="archive_sidebar_upload.tpl"]]

[[+nocache]]
[[+if isset($ARCHIVE_EXAM)]]
    <div id="archive-exam" class="sidebar-element">
        <h3>Eksamen</h3>
        <span class="exam-date">[[+$ARCHIVE_EXAM_PRETTY]]</span>
        <div class="sidebar-element-inner">
            <div class="countdown-wrapper" data-exam="[[+$ARCHIVE_EXAM]]">
            </div>
        </div>
    </div>
[[+/if]]
[[+/nocache]]

<div id="archive-help" class="sidebar-element">
    <h3>Hjelp</h3>
    <div class="sidebar-element-inner">
        <p>Kokeboka skal være lett å bruke. Du laster ned filer ved å klikke på dem. Ønsker du å utforske en mappe trykker du enkelt på mappa.</p>
        <p><a href="hjelp">Se utvidet hjelp for mer informasjon</a>.</p>
        <p><a href="karma">Les mer om karma og hvilke ting som gir deg positiv og negativ uttelling.</a></p>
    </div>
</div>

<div id="archive-history" class="sidebar-element sidebar-element-autoscroll">
    <h3>Historikk</h3>
    <ul class="list-group-hax">
        <li class="list-group-item">Laster...</li>
    </ul>
</div>
