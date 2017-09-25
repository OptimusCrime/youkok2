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
                    url: 'processors/newest-elements',
                    success: function(json) {
                        // Get template
                        var template_sidebar_newest = _.template(
                            $('script.template-sidebar-newest').html()
                        );

                        // Set content
                        $('#archive-sidebar-newest-inner').html(template_sidebar_newest({'elements': json.data}));

                         // Apply moment.js
                        $('#archive-sidebar-newest-inner .moment-timestamp').each(function () {
                            var $that = $(this);
                            $that.html(moment($(this).data('ts')).fromNow());
                        });

                        // Tooltip
                        $('#archive-sidebar-newest-inner .moment-timestamp').tooltip();
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
                            $('script.template-sidebar-popular').html()
                        );

                        // Set content
                        $('#archive-sidebar-popular-inner').html(template_sidebar_popular({'elements': json.data}));

                        // Tooltip
                        $('#archive-sidebar-popular-inner a').tooltip();
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
                            $('script.template-sidebar-commits').html()
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
