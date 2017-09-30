var Youkok = (function (module) {
    
    /*
     * Inits functions for online users
     */
    var initOnline = function() {
        $('#archive-heading-star').on('click', toggleFavorite);
    };
    
    /*
     * Toggle as favorite
     */
    var toggleFavorite = function() {
        // Store
        var $that = $(this);

        // Check which way to favorite
        var favorite_type = 'add';
        
        if ($that.hasClass('archive-heading-star-1')) {
            favorite_type = 'remove';
        }

        // Gogogo request
        $.ajax({
            cache: false,
            type: "post",
            url: 'processor/favorite',
            data: { 
                id: $that.data('archive-id') ,
                type: favorite_type
            },
            success: function(json) {
                if (json.code == 200) {
                    // Everything went better than expected :)
                    if (favorite_type == 'add') {
                        $that.removeClass('archive-heading-star-0').addClass('archive-heading-star-1');
                    }
                    else {
                        $that.removeClass('archive-heading-star-1').addClass('archive-heading-star-0');
                    }

                    // Display message
                    Youkok.message.add(json.msg);
                }
                else {
                    // Something went wrong
                    alert('Noe gikk visst galt her. Ups!');
                }
            }
        });
    };
    
    /*
     * Handles dropdown for archive items
     */
    var itemDropdownOpen = function() {
        var $caret = $('i', this);
        var $dropdown = $('.archive-dropdown-content', this);
        
        if ($dropdown.is(':visible')) {
            $dropdown.slideUp(400, function () {
                $caret.removeClass('fa-caret-up').addClass('fa-caret-down');
            });
        }
        else {
            $dropdown.slideDown(400, function () {
                $caret.removeClass('fa-caret-down').addClass('fa-caret-up');
            });
        }
    };
    var itemDropdownClose = function(e) {
        // Prevent default
        e.preventDefault();
        
        // Trigger click to avoid writing the same fuction twice (duh)
        $(this).parent().parent().parent().parent().find('.archive-item-dropdown-arrow').trigger('click');
    };
    
    /*
     * Fetches history
     */
    var getHistory = function() {
        $.ajax({
            cache: false,
            type: "post",
            url: "processor/history/get",
            data: {
                id: Youkok.getData('archive_id')
            },
            success: function(json) {
                // Check if anything was returned
                if (json.data.length > 0) {
                    // Results, parse with underscorejs
                    var template_sidebar_history = _.template(
                        $('script.template-sidebar-history').html()
                    );

                    // Set content
                    $('#archive-history ul').html(template_sidebar_history({'histories': json.data}));
                }
                else {
                    var template_sidebar_history = _.template(
                        $('script.template-sidebar-no-history').html()
                    );

                    // Set content
                    $('#archive-history ul').html(template_sidebar_history());
                }
            }
        });
    };
    
    /*
     * Public methods
     */
    module.archive = {
        
        /*
         * Init function
         */
        init: function() {
            // Add listeners
            $('.archive-item-dropdown').on('click', itemDropdownOpen);
            $('.archive-dropdown-close').on('click', itemDropdownClose);

            getHistory();
            initOnline();
            
            // Init the counter
            Youkok.countdown.init();
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});