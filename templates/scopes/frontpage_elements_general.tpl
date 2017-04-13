[[+foreach $ELEMENTS as $element]]
    <li class="list-group-item">
        <a
            href="[[+element_url element=[[+$element]] ]]"
            [[+if $element->link !== null]]target="_blank" title="Link til: [[+$element->link]]"[[+/if]]>
            [[+$element->name]]
        </a>

        [[+if $element->parentObj !== null]] @
            [[+if $element->parentObj->parent !== null]]
                <a href="[[+element_url element=[[+$element->parentObj]] ]]">
                    [[+$element->parentObj->name]]
                </a>,
            [[+/if]]

            <a href="[[+element_url element=[[+$element->parentRootObj]] ]]"
                title="[[+$element->parentRootObj->courseName]]" data-placement="top" data-toggle="tooltip">
                [[+$element->parentRootObj->courseCode]]
            </a>
        [[+/if]]

        [[+if $DISPLAY === 'added']]
            [<span class="moment-timestamp help" data-toggle="tooltip" title="[[+$element->addedPrettyAll]]" data-ts="[[+$element->added]]">Laster...</span>]
        [[+elseif $DISPLAY === 'downloads']]
            [?]
        [[+/if]]
    </li>
[[+/foreach]]
