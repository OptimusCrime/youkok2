<script type="text/template" class="template-sidebar-commits">
    <ul class="list-group">
        <% _.each(rc.commits,function(commit) { %>
            <li class="list-group-item"><%- commit %></li>
        <% }); %>
    </ul>
</script>
<script type="text/template" class="template-sidebar-popular">
    <ul class="list-group">
        <% _.each(rc.elements,function(element) { %>
            <li class="list-group-item">
                <a href="<%- element.full_url %>" target="_blank" <% if (element.url !== null) { %>title="Link til: <%- element.url %>"<% } %>>
                    <%- element.name %>
                </a> @ <a href="#">Rofl</a>, <a href="#">rofl</a>
                [xxx]
            </li>
        <% }); %>
    </ul>
</script>