var Youkok = (function (module) {
    /*
     * Private variables
     */
    var courses;
    
    /*
     * Init Bloodhound
     */
    var initBloodhound = function() {
        courses = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('course'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            limit: 10,
            prefetch: 'processor/search/courses.json'
        });
        courses.initialize();
    };
    
    /*
     * Init typeahead
     */
    var initTypeahead = function() {
        // Check if we should clear cache first
        if (Youkok.getData('cache_time') != localStorage.getItem('ts')) {
            // Clear first
            localStorage.clear();
            
            // Assign ts
            localStorage.setItem('ts', Youkok.getData('cache_time'));
        }
        
        // Init typeahead here
        $('#prefetch .typeahead, #prefetch2 .typeahead').typeahead({
            hint: false,
            highlight: true
        }, {
            name: 'courses',
            displayKey: 'course',
            source: courses.ttAdapter()
        }).on('typeahead:selected', function($e, datum) {
            window.location.href = datum.url;
        });
    };
    
    /*
     * Public methods
     */
    module.search = {
        
        /*
         * Init the module
         */
        init: function () {
            // Init search modules
            initBloodhound();
            initTypeahead();
        }
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});