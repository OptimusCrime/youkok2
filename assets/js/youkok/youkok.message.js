var Youkok = (function (module) {
    
    /*
     * Autoclose messages
     */
    var autoClose = function () {
        if ($('.alert').length > 0) {
            // Loop each alert
            $('.alert').each(function () {
                // Check if it can be auto closed
                if (!$(this).hasClass('no-close-auto')) {
                    setTimeout(function (target) {
                        // Close all messages
                        $(target).find('.alert-close').trigger('click');
                    }, 10000, $(this));
                }
            });
        };
    };
    
    /*
     * Close message
     */
    var close = function () {
        // Remove
        $(this).parent().slideUp(400, function () {
            $(this).remove();
        });
    };
    
    /*
     * Public methods
     */
    module.message = {
        
        /*
         * Init messages
         */
        init: function() {
            // Fire auto close
            autoClose();
            
            // Add listener
            $('#main').on('click', '.alert-close', close);
        },
        
        /*
         * Display message
         */
        add: function(msg) {
            var $msg_obj;
            for (var i = 0; i < msg.length; i++) {
                $msg_obj = $('<div class="alert alert-' + msg[i].type + '">' + msg[i].text + '<div class="alert-close"><i class="fa fa-times"></i></div></div>');
                $('#main .row:first').prepend($msg_obj);

                setTimeout(function($msg_obj_inner) {
                    $('.alert-close', $msg_obj_inner).trigger('click');
                }, 10000, $msg_obj);
            }
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});