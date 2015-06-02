var Youkok = (function (module) {
    
    /*
     * Toggle display/collaps for debug queries
     */
    var toggleQueries = function(e) {
        // Prevent default
        e.preventDefault();
        
        // Save scope
        var $that = $(this);
        
        // Hide other
        if ($('#cache-load').is(':visible')) {
            $('#cache-load').slideUp(400, function () {
                $('#toggle-cache-loads span').html('Vis');
            });
        }
        
        // Toggle
        if ($('#queries').is(':visible')) {
            $('#queries').slideUp(400, function () {
                $('span', $that).html('Vis');
            });
        }
        else {
            $('#queries').slideDown(400, function () {
                $('span', $that).html('Skjul');
            });
        }
    };
    
    /*
     * Toggle display/collaps for debug cache
     */
    var toggleCache = function(e) {
        // Prevent default
        e.preventDefault();
        
        // Save scope
        var $that = $(this);
        
        // Hide other
        if ($('#queries').is(':visible')) {
            $('#queries').slideUp(400, function () {
                $('#toggle-queries span').html('Vis');
            });
        }
        
        // Toggle
        if ($('#cache-load').is(':visible')) {
            $('#cache-load').slideUp(400, function () {
                $('span', $that).html('Vis');
            });
        }
        else {
            $('#cache-load').slideDown(400, function () {
                $('span', $that).html('Skjul');
            });
        }
    };
    
    /*
     * Public methods
     */
    module.debug = {
        
        /*
         * Init the module
         */
        init: function () {
            // Add listeners
            $('#toggle-queries').on('click', toggleQueries);
            $('#toggle-cache-loads').on('click', toggleCache);
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});