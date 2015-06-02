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
            prefetch: 'processor/search/courses.json',
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
            hint: true,
            highlight: true,
        }, {
            name: 'courses',
            displayKey: 'course',
            source: courses.ttAdapter(),	
        }).on('typeahead:selected', function($e, datum) {
            submit($e.target.id);
        });
    };
    
    /*
     * Handles enter press in search field
     */
    var enter = function (e) {
        if (e.keyCode == 13) {
            submit(this.id);
        }
    };
    
    /*
     * Handles click on search elements
     */
    var click = function () {
        submit($(this).parent().find('.tt-input').attr('id'));
    };
    
    /*
     * Handle search
     */
    var submit = function(target) {
        // Find what values to use
        var val = $('#' + target).val();
        
        // Some variables
        var datums = courses.index.datums;
        var datums_size = datums.length;
        var was_found = false;

        // Now loop 'em
        for (var i = 0; i < datums_size; i++) {
            // Store reference
            var current_datum = datums[i];

            // Check if match
            if (current_datum.course == val) {
                // Match!
                was_found = true;
                window.location = Youkok.getData('search_base') + current_datum.url;
                break;
            }
        }

        // Check if was found or not
        if (!was_found) {
            $('#' + target).parent().parent().parent().submit();
        }
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
            
            // Add listeners
            $('#s, #s2').on('keyup', enter);
            $('#nav-search, #nav-search2').on('click', click);
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});