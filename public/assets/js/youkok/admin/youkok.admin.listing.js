var Youkok = (function (module) {

    var initShowHide = function() {
        if ($(this).hasClass('fa-caret-down')) {
            $(this).removeClass('fa-caret-down').addClass('fa-caret-right');
            $(this).closest('.admin-file-line').parent().find('ul').slideUp();
        }
        else {
            $(this).removeClass('fa-caret-right').addClass('fa-caret-down');
            $(this).closest('.admin-file-line').parent().find('ul').slideDown();
        }
    };

    /*
     * Public methods
     */
    module.admin.listing = {

        /*
         * Init the module
         */
        init: function () {
            $('body').on('click', '.admin-file-tree-directory', initShowHide);
        }
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});