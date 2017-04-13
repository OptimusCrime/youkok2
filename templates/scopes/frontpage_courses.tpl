[[+foreach $ELEMENTS as $element]]
    <li class="list-group-item">
        <a href="[[+element_url element=[[+$element]] ]]">
            <strong>[[+$element->courseCode]]</strong> &mdash; [[+$element->courseName]]
        </a>

        [[+if $DISPLAY === 'downloads']]
            [?]
        [[+/if]]
    </li>
[[+/foreach]]
