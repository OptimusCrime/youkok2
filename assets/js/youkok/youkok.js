var Youkok = (function (module) {
    
    /*
     * Private variables
     */
    var data; // Holds the site information
    
    /*
     * Init different sub modules
     */
    var initSubModules = function() {
        // Independent sub modules (always loaded)
        Youkok.user.init();
        Youkok.message.init();
        Youkok.general.init();
        Youkok.grayboxes.init();
        Youkok.search.init();
        Youkok.debug.init();
        
        // Archive
        if (Youkok.getData('view') == 'archive') {
            Youkok.archive.init();
            Youkok.createFile.init();
            Youkok.createLink.init();
            
            // Init submodule for logged in archive
            if (Youkok.getData('online') == true) {
                Youkok.createDirectory.init();
            }
        }
        
        // Frontpage
        if (Youkok.getData('view') == 'frontpage') {
            Youkok.frontpage.init();
        }
        
        // Forgotten password
        if (Youkok.getData('view') == 'forgotten-password') {
            Youkok.forgottenPassword.init();
        }
        
        // Register
        if (Youkok.getData('view') == 'register') {
            Youkok.register.init();
        }
    };
    
    /*
     * Init function
     */
    module.init = function () {
        // Parse data
        data = jQuery.parseJSON(SITE_DATA);
        
        // Start moment.js
        moment.lang('en', {
            relativeTime : {
                future: "Om %s",
                past:   "%s siden",
                s:  "Noen få sekunder",
                m:  "Ett minutt",
                mm: "%d minutter",
                h:  "En time",
                hh: "%d timer",
                d:  "en dag",
                dd: "%d dager",
                M:  "En måned",
                MM: "%d måneder",
                y:  "Et år",
                yy: "%d år"
            }
        });
        
        // Apply moment.js
        $('.moment-timestamp').each(function () {
            var $that = $(this);
            $that.html(moment($(this).data('ts')).fromNow());
        });
        
        // Apply tooltips
        $('.list-group-item a, .list-group-item .moment-timestamp').tooltip();
        
        // Init submodules
        initSubModules();
    };
    
    /*
     * Get site data
     */
    module.getData = function (key) {
        return data[key];
    };
    
    /*
     * Return the module
     */
    return module;
})(Youkok || {});