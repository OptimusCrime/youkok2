<ul id="archive-list">
    [[+foreach $ARCHIVE_CONTENT as $element]]
        <li>
            <div class="archive-item-dropdown">
                <div class="archive-item-dropdown-arrow">
                    <i class="fa fa-caret-down"></i>
                </div>
                <div class="archive-dropdown-content">
                    <p>Valg</p>
                    <ul>
                        <li><a href="#" class="archive-dropdown-close">Lukk</a></li>
                    </ul>
                </div>
                <a href="#" title="#">
                    <div class="archive-item">
                        <div class="archive-badge archive-badge-right hidden">
                            <i class="fa fa-comments-o"></i>
                        </div>
                        <div class="archive-item-icon" style="background-image: url('assets/images/icons/');"></div>
                        <div class="archive-item-label">
                            <h4>[[+$element->getName()]]</h4>
                         </div>
                    </div>
                </a>
            </div>
        </li>
    [[+/foreach]]
</ul>