var Youkok = (function (module) {

    /*
     * Public methods
     */
    module.message = {
        
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