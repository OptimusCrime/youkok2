[[+literal]]
<script type="text/template" class="template-sidebar-commits">
    <ul class="list-group">
    <% _.each(rc.commits,function(commit) { %>
        <li class="list-group-item"><%- commit %></li>
    <% }); %>
    </ul>
</script>
<script type="text/template" class="template-sidebar-history">
    <% _.each(rc.histories,function(history) { %>
        <li class="list-group-item"><%= history.history_text %></li>
    <% }); %>
</script>
<script type="text/template" class="template-sidebar-newest">
    <ul class="list-group">
        <% _.each(rc.elements,function(element) { %>
            <li class="list-group-item">
                <a href="<%- element.full_url %>" target="_blank" <% if (element.url !== null) { %>title="Link til: <%- element.url %>"<% } %>>
                    <%- element.name %>
                </a> @ <% if (element.parents.length == 2) { %>
                    <a href="<%- element.parents[0].full_url %>"><%- element.parents[0].name %></a>,
                <% } %>
                <a title="<% if (element.parents.length == 2) { %><%- element.parents[1].course_name %><% } else { %><%- element.parents[0].course_name %><% } %>" data-placement="top" data-toggle="tooltip" href="<% if (element.parents.length == 2) { %><%- element.parents[1].full_url %><% } else { %><%- element.parents[0].full_url %><% } %>"><% if (element.parents.length == 2) { %><%- element.parents[1].course_code %><% } else { %><%- element.parents[0].course_code %><% } %></a>
                [<span class="moment-timestamp help" data-toggle="tooltip" title="<%- element.added_pretty %>" data-ts="<%- element.added %>">Laster...</span>]
            </li>
        <% }); %>
    </ul>
</script>
<script type="text/template" class="template-sidebar-popular">
    <ul class="list-group">
        <% _.each(rc.elements,function(element) { %>
            <li class="list-group-item">
                <a href="<%- element.full_url %>" target="_blank" <% if (element.url !== null) { %>title="Link til: <%- element.url %>"<% } %>>
                    <%- element.name %>
                </a> @ <% if (element.parents.length == 2) { %>
                    <a href="<%- element.parents[0].full_url %>"><%- element.parents[0].name %></a>,
                <% } %>
                <a title="<% if (element.parents.length == 2) { %><%- element.parents[1].course_name %><% } else { %><%- element.parents[0].course_name %><% } %>" data-placement="top" data-toggle="tooltip" href="<% if (element.parents.length == 2) { %><%- element.parents[1].full_url %><% } else { %><%- element.parents[0].full_url %><% } %>"><% if (element.parents.length == 2) { %><%- element.parents[1].course_code %><% } else { %><%- element.parents[0].course_code %><% } %></a>
                [<% if (element.download_count !== null) { %><%- element.download_count %><% } else { %>0<% } %>]
            </li>
        <% }); %>
    </ul>
</script>
[[+/literal]]