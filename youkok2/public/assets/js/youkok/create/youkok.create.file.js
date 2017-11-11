var Youkok = (function (module) {
    /*
     * Private variables
     */
    var num_of_files_uploaded = 0;
    
    /*
     * Init Fileuploader
     */
    var fileuploaderInit = function () {
        $('#archive-create-file-form').fileupload({
            url: $('#archive-create-file-div').data('url') + '?parent=' + Youkok.getData('archive_id'),
            add: fileuploaderAdd,
            done: fileuploaderDone,
            progressall: fileuploaderProgressAll
        });
    };
    
    /*
     * Add file to Fileuploader
     */
    var fileuploaderAdd = function (e, data) {
        // Get file object
        var file_object = data.files[data.files.length - 1];

        // Get data and stuff
        var file_valid = false;
        var file_name = file_object.name;
        var file_type_split = file_object.name.split('.');
        var file_type = file_type_split[file_type_split.length - 1].toLowerCase();
        var accepted_files = Youkok.getData('file_types');
        
        // Check if file has valid file ending
        for (var i = 0; i < accepted_files.length; i++) {
            if (accepted_files[i] == file_type) {
                // Avlid type, save and break out of loop
                file_valid = true;
                break;
            }
        }
        
        // Check if valid file type
        if (file_valid) {
            // Build html
            var container_html  = '<div class="fileupload-file">';
                container_html += '    <strong>';
                container_html +=          file_name;
                container_html += '    </strong>';
                container_html += '    <p>';
                container_html +=          Youkok.utilities.prettyFileSize(file_object.size, true);
                container_html += '    </p>';
                container_html += '    <div class="fileupload-file-remove">';
                container_html += '        <span>Fjern <i class="fa fa-times"></i></span>';
                container_html += '    </div>';
                container_html += '</div>';
            
            // The container
            var $container = $(container_html);

            // The button
            var $button = $('<button style="display: none;">x</button>');

            // Add button to container
            $container.append($button);

            // Set button as context for the file
            data.context = $button;

            // Add listener for the button
            $button.click(function () {
                data.submit();
            });

            // Add container to html
            $('#fileupload-files-inner').append($container);
        }
        else {
            // Display error, wrong filetype
            var error = [
                {
                    'text' : '<p>Filtypen .' + file_type + ' er ikke godkjent på Youkok2. Dersom du syntes denne \
                    filtypen burde godkjennes, kan du ta kontakt. Kontaktinformasjon nederst på siden.</p>',
                    'type' : 'danger',
                },
            ];
            
            // Display message
            Youkok.message.add(error);
        }
    };
    
    /*
     * Fileuploader done
     */
    var fileuploaderDone = function (e, data) {
        // Update number of finished uploads
        num_of_files_uploaded++;
        
        // Check if all done
        if (num_of_files_uploaded == ($('.fileupload-file').length - 1)) {
            var message = [
                {
                    'text' : '<p>Takk for ditt bidrag. Bidraget ditt er sent til godkjenning. Så fort det er godkjent vil det vises på siden.</p>',
                    'type' : 'success'
                }
            ];

            // Display message
            Youkok.message.add(message);

            resetForm();
        }
    };

    var resetForm = function() {
        $('#fileupload-files-inner div').remove();
        $('#archive-create-file-form-submit').html('Last opp').prop('disabled', false);
        $('#archive-create-file-div').slideUp();
    };
    
    /*
     * Fileuploader progress
     */
    var fileuploaderProgressAll = function (e, data) {
        var progress = parseInt(data.loaded / data.total) * 100;
        $('#progress .bar').css({width: progress + '%'});
    };
    
    /*
     * Expand create link div
     */
    var expand = function () {
        if ($('#archive-create-file-div').is(':visible')) {
            $('#archive-create-file-div').stop().slideUp();
        }
        else {
            $('#archive-create-folder-div').stop().slideUp();
            $('#archive-create-link-div').stop().slideUp();
            $('#archive-create-file-div').stop().slideDown();
        }
    };
    
    /*
     * Collaps create link div
     */
    var collaps = function(e) {
        if ($(this).attr('href') == '#') {
            e.preventDefault();
            $('#archive-create-file-div').stop().slideUp(400);
        }
    };
    
    /*
     * Remove
     */
    var remove = function() {
        $(this).parent().remove();
    };
    
    /*
     * Submit form
     */
    var submit = function () {
        // Loop all the buttons
        $('#fileupload-files-inner button').each(function () {
            // Trigger click
            $(this).trigger('click');
        });

        // Update submit button
        $('#archive-create-file-form-submit').html('Laster opp....').prop('disabled', true);

        // Avoid submitting the form
        return false;
    };
    
    /*
     * Public methods
     */
    module.createFile = {
        
        /*
         * Init the module
         */
        init: function () {
            // Add listeners
            $('#archive-create-file').on('click', expand);
            $('#archive-create-file-div a').on('click', collaps);
            $('#fileupload-files').on('click', '.fileupload-file-remove', remove);
            $('#archive-create-file-form').on('submit', submit);
            
            // Init Fileuploader
            fileuploaderInit();
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});