var Youkok = (function (module) {

    /*
     * Public methods
     */
    module.grayboxes = {
        
        /*
         * Init the module
         */
        init: function () {
            // Init newest
            if ($('#archive-sidebar-newest-inner').length > 0) {
                $.ajax({
                    cache: false,
                    url: 'processor/graybox/newest',
                    success: function(html) {
                        // Set content
                        $('#archive-sidebar-newest-inner').html(html);

                        // Load moment
                        $('#archive-sidebar-newest-inner .moment-timestamp').each(function () {
                            $that = $(this);
                            $that.html(moment($(this).data('ts')).fromNow());
                        });
                    }
                });
            }
            
            // Init last downloads
            if ($('#archive-sidebar-popular-inner').length > 0) {
                $.ajax({
                    cache: false,
                    url: 'processor/graybox/popular',
                    success: function(json) {
                        // Get template
                        var template_sidebar_popular = _.template(
                            $( "script.template-sidebar-popular" ).html()
                        );

                        // Set content
                        $('#archive-sidebar-popular-inner').html(template_sidebar_popular({'elements': json.data}));
                    }
                });
            }
            
            // Init numbers
            if ($('#archive-sidebar-numbers-inner').length > 0) {
                $.ajax({
                    cache: false,
                    url: 'processor/graybox/commits',
                    success: function(json) {
                        // Get template
                        var template_sidebar_commits = _.template(
                            $( "script.template-sidebar-commits" ).html()
                        );

                        // Set content
                        $('#archive-sidebar-numbers-inner').html(template_sidebar_commits({'commits': json.data}));
                    }
                });
            }
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});