var Youkok = (function (module) {
    /*
     * Private variables
     */
    var autocomplete_title_xhr = null;
    var working = false;
    
    /*
     * Expand create link div
     */
    var expand = function () {
        if ($('#archive-create-link-div').is(':visible')) {
            $('#archive-create-link-div').stop().slideUp();
        }
        else {
            $('#archive-create-link-div').stop().slideDown(400, function () {
                $('archive-create-link-url').focus();
            });
            $('#archive-create-file-div').stop().slideUp();
            $('#archive-create-folder-div').stop().slideUp();
        }
    };
    
    /*
     * Collaps create link div
     */
    var collaps = function(e) {
        e.preventDefault();
        $('#archive-create-link-div').stop().slideUp(400, function () {
            // Reset form values
            $('#archive-create-link-url').val('');
            $('#archive-create-link-name').val('');
            
            // Reset form
            $('#archive-create-link-name-holder').hide();
            $('#archive-create-link-form-submit').html('Post link').prop('disabled', true);
        });
    };
    
    /*
     * Keyup handler for create link
     */
    var keyup = function() {
        var link_url = $(this).val();
        if (Youkok.utilities.validateUrl(link_url)) {
            // Valid link, try to fetch title
            $('#archive-create-link-form-submit').html('Vent litt').prop('disabled', true);
            
            // Abort previous request
            if (autocomplete_title_xhr !== null) {
                autocomplete_title_xhr.abort();
            }
            
            // Send request
            autocomplete_title_xhr = $.ajax({
                cache: false,
                type: 'post',
                url: 'processors/link/title',
                data: { 
                    url: link_url,
                },
                success: function(json) {
                    if (json.code == 200) {
                        // Found a title, put in the box
                        $('#archive-create-link-name').val(json.title);
                    }
                    else if (json.code == 400) {
                        // Found no title for this URL
                        $('#archive-create-link-name').val('');
                    }
                    
                    // Display if hidden
                    if ($('#archive-create-link-name-holder').is(':hidden')) {
                        $('#archive-create-link-name-holder').slideDown(400, function () {
                            // Turn off disabled
                            $('#archive-create-link-form-submit').html('Post link').prop('disabled', false);
                        });
                    }
                    else {
                        // Turn off disabled
                        $('#archive-create-link-form-submit').html('Post link').prop('disabled', false);
                    }
                }
            });
        }
        else {
            // Not valid url
            $('#archive-create-link-form-submit').html('Ikke gyldig link').prop('disabled', true);
        }
    };
    
    /*
     * Submit create link form
     */
    var submit = function () {
        // Get url
        var link_name = $('#archive-create-link-url').val();
        
        if (!Youkok.utilities.validateUrl(link_name)) {
            Youkok.message.add([{'text': '\'' + link_name + ' \' er ikke en gyldig URL.', 'type': 'danger'}]);
        }
        else {
            // Update queue
            if (!working) {
                working = true;
                
                // Update working
                $('#archive-create-link-form-submit').html('Jobber...').prop('disabled', true);
                
                // Send ajax request
                $.ajax({
                    cache: false,
                    type: 'post',
                    url: $('#archive-create-link-div').data('url'),
                    data: { 
                        id: Youkok.getData('archive_id'), 
                        url: $('#archive-create-link-url').val(),
                        name: $('#archive-create-link-name').val() 
                    },
                    success: function(json) {
                        working = false;
                        if (json.code === 200) {
                            Youkok.message.add([{'text': 'Linken er lagt til. Den blir synlig for andre brukere så fort den blir godkjent. Takk for ditt bidrag.', 'type': 'success'}]);
                            $('#archive-create-link-url').val('');
                            $('#archive-create-link-name').val('');
                            $('#archive-create-link-form-submit').html('Post link').prop('disabled', false);
                        }
                        else if (json.code === 401) {
                            Youkok.message.add([{'text': 'Navnet på linken er for kort. Minst 4 tegn. La feltet så tomt hvis du ønsker å bruke URLen.', 'type': 'danger'}]);
                            $('#archive-create-link-form-submit').html('Post link').prop('disabled', false);
                        }
                        else {
                            Youkok.message.add([{'text': 'Noe gikk visst galt her!', 'type': 'danger'}]);
                            $('#archive-create-link-form-submit').html('Post link').prop('disabled', false);
                        }
                    }
                });
            }
        }
        
        // Avoid submitting form
        return false;
    };
    
    /*
     * Public methods
     */
    module.createLink = {
        
        /*
         * Init the module
         */
        init: function () {
            // Add listeners
            $('#archive-create-link').on('click', expand);
            $('#archive-create-link-div a').on('click', collaps);
            $('#archive-create-link-url').on('keyup', keyup);
            $('#archive-create-link-form').on('submit', submit);
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});