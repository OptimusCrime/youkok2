var Youkok = (function (module) {
    
    /*
     * Listener for login stuff
     */
    var addLoginListeners = function () {
        $('#dropdown-menu-opener').on('click', function(e) {
            setTimeout(function() {
                $('#login-email').focus();
            }, 100);
        });
    };
    
    /*
     * Listeners for the profile
     */
    var addProfileListeners = function () {
        $('#profile-edit-info-form input').on('keyup', function () {
            if (this.id == 'register-form-email') {
                var $that = $(this);
                var $that_parent = $that.parent();
                
                // Check for valid email
                if (Youkok.utilities.validateEmail($that.val())) {
                    if ($that_parent.hasClass('has-error')) {
                        $that_parent.removeClass('has-error');
                    }
                    
                    // Reset error
                    $('#register-form-email-error1').css('color', '');
                    $('#register-form-email-error2').css('color', '');
                    
                    // Check if new email
                    if ($('#register-form-email').data('original') != $('#register-form-email').val()) {

                        $.ajax({
                            cache: false,
                            type: "post",
                            url: "processor/register/email",
                            data: { 
                                email: $('#register-form-email').val(), 
                                ignore: true 
                            },
                            success: function(json) {
                                if (json.code == 200) {
                                    $('#register-form-email-error2').css('color', '');
                                }
                                else {
                                    $('#register-form-email-error2').css('color', 'red');
                                }
                            }
                        });
                    }
                }
                else {
                    if (!$that_parent.hasClass('has-error')) {
                        $that_parent.addClass('has-error');
                    }
                    
                    // Set error states
                    $('#register-form-email-error1').css('color', 'red');
                    $('#register-form-email-error2').css('color', '');
                }
            }
        });
    };
    
    /*
     * Public methods
     */
    module.user = {
        
        /*
         * Init the module
         */
        init: function () {
            // Add login listeners
            addLoginListeners();
            
            // Add profile listeners
            if (Youkok.getData('online') == true) {
                addProfileListeners();
            }
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});