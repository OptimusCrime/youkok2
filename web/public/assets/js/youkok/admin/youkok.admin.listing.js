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

    var showEditModal = function(e) {
        e.preventDefault();

        $('#element-modal').modal('show');
        $('#element-modal-title').html('Laster...');
        $('#element-modal input').prop('disabled', true);

        $.ajax({
            cache: false,
            url: $(this).data('url'),
            success: function(json) {
                if (json.code === 400) {
                    alert('Someting went wrong');
                }
                else {
                    if (json.name.indexOf('||') > -1) {
                        var names_split = json.name.split('||');
                        $('#element-modal-title').html(names_split[0] + ' &mdash; ' + names_split[1]);
                    }
                    else {
                        $('#element-modal-title').html(json.name);
                    }

                    $('#element-parent option').remove();
                    for (var i = 0; i < json.parents.length; i++) {
                        $('#element-parent').append($('<option/>', {
                            value: json.parents[i].value,
                            text : json.parents[i].text,
                            disabled: json.parents[i].disabled
                        }));
                    }

                    $('#element-id').val(json.id);
                    $('#element-name').val(json.name);
                    $('#element-slug').val(json.slug);
                    $('#element-uri').val(json.uri);
                    $('#element-parent').val(json.parent);
                    $('#element-empty').prop('checked', json.empty === 1);
                    $('#element-checksum').val(json.checksum);
                    $('#element-size').val(json.size);
                    $('#element-directory').prop('checked', json.directory === 1);
                    $('#element-pending').prop('checked', json.pending === 1);
                    $('#element-deleted').prop('checked', json.deleted === 1);
                    $('#element-link').val(json.link);
                    $('#element-added').val(json.added);
                    $('#element-last-visited').val(json.last_visited);

                    $('#element-modal input').prop('disabled', false);
                }
            }
        });
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
            $('body').on('click', '.admin-tree-edit', showEditModal);
        }
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});