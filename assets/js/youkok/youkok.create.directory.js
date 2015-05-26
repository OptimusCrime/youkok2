var Youkok = (function (module) {
    
    /*
     * Private variables
     */
    var working = false;
    
    /*
     * Public methods
     */
    module.createDirectory = {
        
        /*
         * Init the module
         */
        init: function () {
            // Add listeners
            $('#archive-create-folder').on('click', Youkok.createDirectory.expand);
            $('#archive-create-folder-div a').on('click', Youkok.createDirectory.collaps);
            $('#archive-create-folder-name').on('keyup', Youkok.createDirectory.keyup);
            $('#archive-create-folder-form').on('submit', Youkok.createDirectory.submit);
        },
        
        /*
         * Expand create directory div
         */
        expand: function () {
            if ($('#archive-create-folder-div').is(':visible')) {
                $('#archive-create-folder-div').stop().slideUp();
            }
            else {
                $('#archive-create-folder-div').stop().slideDown(400, function () {
                    $('archive-create-folder-name').focus();
                });
                $('#archive-create-file-div').stop().slideUp();
                $('#archive-create-link-div').stop().slideUp();
            }
        },
        
        /*
         * Collaps create directory div
         */
        collaps: function(e) {
            e.preventDefault();
            $('#archive-create-folder-div').stop().slideUp(400, function () {
                $('#archive-create-folder-name').val('');
            });
        },
        
        /*
         * KeyUp handler
         */
        keyup: function() {
            if (!working && $(this).val().length > 3) {
                $('#archive-create-folder-form-submit').prop('disabled', false);
            }
            else {
                $('#archive-create-folder-form-submit').prop('disabled', true);
            }
        },
        
        /*
         * Submit create directory form
         */
        submit: function () {
            if ($('#archive-create-folder-name').val().length == 0) {
                alert('Error: Du har ikke gitt mappen noen navn!');
            }
            else {
                // Update queue
                if (!working) {
                    working = true;

                    // Update working
                    $('#archive-create-folder-form-submit').html('Jobber...').prop('disabled', true);

                    $.ajax({
                        cache: false,
                        type: "post",
                        url: 'processor/folder/create',
                        data: { 
                            id: Youkok.getData('archive_id'), 
                            name: $('#archive-create-folder-name').val() 
                        },
                        success: function(json) {
                            if (json.code == 200) {
                                // Refresh
                                window.location.reload();
                            }
                            else if (json.code == 400) {
                                working = false;
                                Youkok.message.add([{'text': 'Navnet på elementet er fort kort. Minst 4 tegn.', 'type': 'danger'}]);
                                $('#archive-create-folder-form-submit').html('Lagre').prop('disabled', false);
                            }
                            else {
                                Youkok.message.add([{'text': 'Noe gikk visst galt her!', 'type': 'danger'}]);
                                $('#archive-create-folder-form-submit').html('Lagre').prop('disabled', false);
                            }
                        }
                    });
                }
            }
            
            // Avoid submit form
            return false;
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});