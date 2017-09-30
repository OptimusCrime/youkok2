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
        Youkok.message.init();
        Youkok.general.init();
        Youkok.grayboxes.init();
        Youkok.search.init();
        Youkok.debug.init();
        
        // Archive
        if ($('body').hasClass('archive')) {
            Youkok.archive.init();
            Youkok.createFile.init();
            Youkok.createLink.init();
        }
        
        // Frontpage
        if (Youkok.getData('view') == 'frontpage') {
            Youkok.frontpage.init();
        }
        
        // Admin
        if ($('body').hasClass('admin')) {
            Youkok.admin.init();
        }
    };
    
    /*
     * Init function
     */
    module.init = function () {
        // Parse data
        data = jQuery.parseJSON(SITE_DATA);
        
        // Start moment.js
        moment.locale('en', {
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

        // Underscorejs config
        _.templateSettings.variable = 'rc';
        
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
