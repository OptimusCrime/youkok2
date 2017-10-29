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
            prefetch: 'processors/autocomplete'
        });
        courses.initialize();
    };

    /*
     * Init typeahead
     */
    var initTypeahead = function() {
        // Init typeahead here
        $('#admin-filter .typeahead').typeahead({
            hint: true,
            highlight: true
        }, {
            name: 'courses',
            displayKey: 'course',
            source: courses.ttAdapter()
        }).on('typeahead:selected', function($e, datum) {
            var selected = datum.course;
            $('.youkok-file-box').each(function() {
               if ($(this).data('identifier') === selected) {
                   $(this).show();
               }
               else {
                   $(this).hide();
               }
            });
        });
    };

    var initReset = function() {
        $('#reset-filter').on('click', function () {
            $('#s2').val('');
            $('.youkok-file-box:hidden').each(function() {
                $(this).show();
            });
        });
    };

    /*
     * Public methods
     */
    module.admin.filter = {

        /*
         * Init the module
         */
        init: function () {
            // Init search modules
            initBloodhound();
            initTypeahead();
            initReset();
        }
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});