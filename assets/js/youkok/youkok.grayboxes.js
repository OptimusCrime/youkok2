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
                    url: 'graybox/newest',
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
            if ($('#archive-sidebar-last-downloads-inner').length > 0) {
                $.ajax({
                    cache: false,
                    url: 'graybox/downloads',
                    success: function(html) {
                        // Set content
                        $('#archive-sidebar-last-downloads-inner').html(html);

                        // Load moment
                        $('#archive-sidebar-last-downloads-inner .moment-timestamp').each(function () {
                            $that = $(this);
                            $that.html(moment($(this).data('ts')).fromNow());
                        });
                    }
                });
            }
            
            // Init numbers
            if ($('#archive-sidebar-numbers-inner').length > 0) {
                $.ajax({
                    cache: false,
                    url: 'graybox/numbers',
                    success: function(html) {
                        // Set content
                        $('#archive-sidebar-numbers-inner').html(html);
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