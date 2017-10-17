var Youkok = (function (module) {

    var loadMostPopularElements = function () {
        $.ajax({
            cache: false,
            url: $('#popular-elements-box').data('url'),
            success: function(json) {
                // Get template
                var template_sidebar_newest = _.template(
                    $('script.template-frontpage-popular-elements').html()
                );

                // Set content
                $('#popular-elements-box div').html(template_sidebar_newest({'elements': json.data}));

                // Apply moment.js
                $('#popular-elements-box').find('.moment-timestamp').each(function () {
                    var $that = $(this);
                    $that.html(moment($(this).data('ts')).fromNow());
                });

                // Tooltip
                $('#popular-elements-box').find('.moment-timestamp').tooltip();
            }
        });
    };

    var loadNewestElements = function () {
        $.ajax({
            cache: false,
            url: $('#newest-elements-box').data('url'),
            success: function(json) {
                // Get template
                var template_sidebar_newest = _.template(
                    $('script.template-frontpage-popular-elements').html()
                );

                // Set content
                $('#newest-elements-box div').html(template_sidebar_newest({'elements': json.data}));

                // Apply moment.js
                $('#newest-elements-box').find('.moment-timestamp').each(function () {
                    var $that = $(this);
                    $that.html(moment($(this).data('ts')).fromNow());
                });

                // Tooltip
                $('#newest-elements-box').find('.moment-timestamp').tooltip();
            }
        });
    };

    /*
     * Public methods
     */
    module.grayboxes = {

        /*
         * Init the module
         */
        init: function () {
            // Init newest
            if ($('#popular-elements-box').length > 0) {
                loadMostPopularElements();
            }
            if ($('#newest-elements-box').length > 0) {
                loadNewestElements();
            }
        }
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});
