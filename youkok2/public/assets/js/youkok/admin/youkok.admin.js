/*
 * The Youkok2 admin module
 */

var Youkok = (function (module) {
    
    // Make sure we have a admin module
    module.admin = module.admin || {};
    
    /*
     * Apply the generic event listeners
     */
    
    var initGenericListeners = function () {
        // Collaps/Expand modules
        $('[data-widget="collapse"]').on('click', function (e) {
            e.preventDefault();
            
            $(this).closest('.box').find('.box-body, .box-footer').each(function () {
                if ($(this).is(':visible')) {
                    $(this).stop().slideUp();
                }
                else {
                    $(this).stop().slideDown();
                }
            });
        });
        
        // Collaps/Expand sidebar items
        $('.treeview a').on('click', function (e) {
            var $obj = $(this);
            
            // Make sure to only fire on sidebar expand/collaps elements
            if ($obj.attr('href') == '#') {
                e.preventDefault();
                
                // Some stuff
                var $parent = $obj.parent();
                var $grand_parent = $parent.parent();
                var $submenu = $parent.find('ul').eq(0);
                
                // Make sure we have a submenu
                if ($submenu) {
                    // Check if we should expand or collaps
                    if ($submenu.is(':visible')) {
                        // Collaps child submenu
                        $submenu.stop().slideUp(400, function () {
                            // Remove active class
                            $parent.removeClass('active');
                            
                            // Collaps all inner submenus too
                            $(this).find('ul').hide();
                            $(this).find('li.active').removeClass('active');
                        });
                    }
                    else {
                        // Collaps already expanded submenus
                        $grand_parent.find('.treeview.active > ul:visible').stop().slideUp(400, function () {
                            // Remove active class
                            $(this).parent().removeClass('active');
                            
                            // Collaps all inner submenus too
                            $(this).parent().find('ul').hide();
                            $(this).parent().find('li.active').removeClass('active');
                        });
                        
                        $submenu.stop().slideDown(400, function () {
                            $parent.addClass('active');
                        });
                    }
                }
            }
        });
        
        // Expand/Collaps sidebar
        $('.sidebar-toggle').on('click', function (e) {
            e.preventDefault();
            
            var $body = $('body');
            if ($body.hasClass('sidebar-collapse')) {
                $body.removeClass('sidebar-collapse');
            }
            else {
                $body.addClass('sidebar-collapse');
            }
        });
    };
    
    /*
     * Init the subviews
     */
    
    var initSubViews = function () {
        // Archive
        if (Youkok.getData('view') === 'admin_home') {
            Youkok.admin.home.init();
        }

        if (Youkok.getData('view') === 'admin_files' || Youkok.getData('view') === 'admin_pending') {
            Youkok.admin.listing.init();
            Youkok.admin.filter.init();
        }
    };
    
    /*
     * Public methods
     */
    
    module.admin.init = function () {
        // Init the generic listeners
        initGenericListeners();
        
        // Init subviews
        initSubViews();
    };
    
    /*
     * Return the module
     */
    
    return module;
})(Youkok || {});
