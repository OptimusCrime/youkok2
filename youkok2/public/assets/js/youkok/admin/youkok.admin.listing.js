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
        $('#element-modal-save-message').stop().hide();
        $('#element-modal-title').html('Laster...');
        $('#element-modal input').prop('disabled', true);
        $('#element-modal input[type="text"], #element-modal input[type="numer"], #element-modal input[type="url"]').val('');
        $('#element-modal input[type="checkbox"]').prop('checked', false);
        $('#element-modal .has-success, #element-modal .has-error').removeClass('has-success has-error');
        $('#element-modal .help-block').remove();

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

                    $('#element-modal-slug-regenerate, #element-modal-uri-regenerate').data('id', json.id);

                    $('#element-name').val(json.name);
                    $('#element-slug').val(json.slug);
                    $('#element-uri').val(json.uri);
                    $('#element-parent').val(json.parent);
                    $('#element-empty').prop('checked', json.empty === 1);
                    $('#element-checksum').val(json.checksum);

                    if (json.checksum_verified === true) {
                        $('#element-checksum').parent().append('<span class="help-block">File in place.</span>');
                        $('#element-checksum').parent().addClass('has-success');
                    }
                    else if (json.checksum_verified === false) {
                        $('#element-checksum').parent().append('<span class="help-block">File is missing.</span>');
                        $('#element-checksum').parent().addClass('has-error');
                    }

                    $('#element-size').val(json.size);
                    $('#element-directory').prop('checked', json.directory === 1);
                    $('#element-pending').prop('checked', json.pending === 1);
                    $('#element-deleted').prop('checked', json.deleted === 1);
                    $('#element-link').val(json.link);
                    $('#element-added').val(json.added);
                    $('#element-last-visited').val(json.last_visited);

                    $('#element-modal input').prop('disabled', false);

                    if (json.directory === 1) {
                        $('#element-checksum, #element-link, #element-size').prop('disabled', true);
                    }
                    if (json.link !== null) {
                        $('#element-checksum, #element-uri, #element-slug, #element-size').prop('disabled', true);
                    }
                }
            }
        });
    };

    var regenElementField = function(e) {
        e.preventDefault();
    };

    var refreshContent = function(id, url) {
        $('#element-container-' + id).css('opacity', 0.2);

        $.ajax({
            cache: false,
            url: url,
            success: function (json) {
                if (json.code === 200) {
                    $('#element-container-' + id).html(json.html);
                }
                else {
                    alert('Something went wrong');
                }

                $('#element-container-' + id).css('opacity', 1);
            }
        });
    };

    var refreshPending = function(id, url) {
        $('#pending-container-' + id).css('opacity', 0.2);

        $.ajax({
            cache: false,
            url: url,
            success: function (json) {
                if (json.code === 200) {
                    $('#pending-container-' + id).html(json.html);
                }
                else {
                    alert('Something went wrong');
                }

                $('#pending-container-' + id).css('opacity', 1);
            }
        });
    };

    var saveModal = function(e) {
        e.preventDefault();

        var close = this.id === 'element-modal-save-close';

        $.ajax({
            cache: false,
            type: 'put',
            url: $(this).data('url'),
            data: $('#element-modal-form').serialize(),
            success: function(json) {
                if (json.code === 400) {
                    alert('Someting went wrong');
                }
                else {
                    refreshContent(json.course, json.action);

                    if (Youkok.getData('view') === 'admin_pending') {
                        refreshPending(json.course, json.action_pending);
                    }

                    if (close) {
                        $('#element-modal').modal('hide');
                    }
                    else {
                        $('#element-modal-save-message').css('display', 'inline-block').delay(6000).queue(function (next) {
                            $(this).hide();
                            next();
                        });
                    }
                }
            }
        });
    };

    var regenUri = function(e) {
        e.preventDefault();

        $.ajax({
            cache: false,
            type: 'put',
            url: $(this).data('url'),
            data: {
                'id': $('#element-id').val()
            },
            success: function(json) {
                if (json.code === 400) {
                    alert('Someting went wrong');
                }
                else {
                    $('#element-uri').val(json.uri);
                }
            }
        });
    };

    var addChild = function(e) {
        e.preventDefault();

        $('#directory-modal').modal('show');
        $('#directory-modal-title').html('Opprett undermappe til ' + $(this).data('name'));
        $('#directory-parent').val($(this).data('id'));
        $('#directory-name').val('');
    };

    var addElement = function(e) {
        e.preventDefault();

        if ($('#directory-name').length === 0) {
            alert('Gi mappen et navn');
            return;
        }

        $.ajax({
            cache: false,
            type: 'post',
            url: $(this).data('url'),
            data: $('#directory-modal-form').serialize(),
            success: function(json) {
                if (json.code === 400) {
                    alert('Someting went wrong');
                }
                else {
                    refreshContent(json.course, json.action);
                    $('#directory-modal').modal('hide');
                }
            }
        });

    };

    var submitElementForm = function(e) {
        e.preventDefault();

        $('#element-modal-save-close').trigger('click');
        return false;
    };

    var submitDirectoryForm = function(e) {
        e.preventDefault();

        $('#directory-modal-save-close').trigger('click');
        return false;
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
            $('body').on('click', '.admin-tree-edit, .admin-course-edit', showEditModal);
            $('body').on('click', '#element-modal-slug-regenerate, #element-modal-uri-regenerate', regenElementField);
            $('body').on('click', '#element-modal-save, #element-modal-save-close', saveModal);
            $('body').on('click', '#directory-modal-save-close', addElement);
            $('body').on('click', '#element-modal-uri-regenerate', regenUri);
            $('body').on('click', '.admin-tree-add-child', addChild);
            $('body').on('submit', '#element-modal-form', submitElementForm);
            $('body').on('submit', '#directory-modal-form', submitDirectoryForm);
        }
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});