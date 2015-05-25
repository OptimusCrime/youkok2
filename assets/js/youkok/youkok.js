var Youkok = (function (module) {
    
    /*
     * Private variables
     */
    var data; // Holds the site information
    var courses; // Holds Bloodhound
    
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
        
        // Start Bloodhound
        courses = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('course'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            limit: 10,
            prefetch: 'processor/search/courses.json',
        });
        courses.initialize();
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