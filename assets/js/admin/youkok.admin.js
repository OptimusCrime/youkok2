/*
 * The Youkok2 admin module
 */

var Youkok2Admin = (function () {
    
    /*
     * Apply the generic event listeners
     */
    
    var applyEventListeners = function () {
        // Collaps/Expand
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
    };
    
    /*
     * Public methods
     */
    
    return {
        
        /*
         * Init the module
         */
        
        init: function () {
            applyEventListeners();
        }
    };
})();

/*
 * jQuery goes here
 */

$(document).ready(function () {
    Youkok2Admin.init();
});