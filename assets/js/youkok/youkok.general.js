var Youkok = (function (module) {

    /*
     * Public methods
     */
    module.general = {
        
        /*
         * Init the module
         */
        init: function () {
            // Add listener for login stuff
            $('#login-dropdown label, #login-dropdown input').on('click', function(e) {
                e.stopPropagation();
            });
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});