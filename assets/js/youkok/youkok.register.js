var Youkok = (function (module) {

    /*
     * Public methods
     */
    module.register = {
        
        /*
         * Init the module
         */
        init: function () {
            var register_email_checked = false;
            var register_email_value = "";
            $('#register-form input').on('keyup', function () {
                var $that = $(this);
                var $that_parent = $that.parent();
                var element_id = this.id;

                if (this.id == 'register-form-email') {
                    if (Youkok.utilities.validateEmail($that.val())) {
                        if ($that_parent.hasClass('has-error')) {
                            $that_parent.removeClass('has-error');
                        }
                        $('#register-form-email-error1').css('color', '');

                        if (register_email_value != $('#register-form-email').val()) {
                            register_email_value = $('#register-form-email').val();
                            register_email_checked = false;

                            $.ajax({
                                cache: false,
                                type: "post",
                                url: "processor/register/email",
                                data: { 
                                    email: $('#register-form-email').val() 
                                },
                                success: function(json) {
                                    if (json.code == 200) {
                                        register_email_checked = true;
                                        $('#register-form-email-error2').css('color', '');
                                        $('#register-form-email').trigger('keyup');
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
                        $('#register-form-email-error1').css('color', 'red');
                    }
                }
                else if (this.id == 'register-form-password1') {
                    if ($that.val().length < 7) {
                        if (!$that_parent.hasClass('has-error')) {
                            $that_parent.addClass('has-error');
                        }
                        $('#register-form-password-error1').css('color', 'red');
                        $('#register-form-password2').prop('disabled', true).val('').parent().removeClass('has-error');
                        $('#register-form-password-error2').css('color', '');
                    }
                    else {
                        if ($that_parent.hasClass('has-error')) {
                            $that_parent.removeClass('has-error');
                        }
                        $('#register-form-password-error1').css('color', '');
                        $('#register-form-password2').prop('disabled', false);
                    }

                    if ($('#register-form-password2').val().length != 0) {
                        if ($('#register-form-password1').val() != $('#register-form-password2').val()) {
                            if (!$('#register-form-password2').parent().hasClass('has-error')) {
                                $('#register-form-password2').parent().addClass('has-error');
                            }
                            $('#register-form-password-error2').css('color', 'red');
                        }
                        else {
                            if ($('#register-form-password2').parent().hasClass('has-error')) {
                                $('#register-form-password2').parent().removeClass('has-error');
                            }
                            $('#register-form-password-error2').css('color', '');
                        }
                    }
                }
                else {
                    if ($('#register-form-password1').val() != $('#register-form-password2').val()) {
                        if (!$that_parent.hasClass('has-error')) {
                            $that_parent.addClass('has-error');
                        }
                        $('#register-form-password-error2').css('color', 'red');
                    }
                    else {
                        if ($that_parent.hasClass('has-error')) {
                            $that_parent.removeClass('has-error');
                        }
                        $('#register-form-password-error2').css('color', '');
                    }
                }

                if ($('#register-form .has-error').length == 0 && register_email_checked == true && Youkok.utilities.validateEmail($('#register-form-email').val()) && $('#register-form-password1').val().length > 6 && $('#register-form-ret').is(':checked')) {
                    $('#register-form-submit').prop('disabled', false);
                }
                else {
                    $('#register-form-submit').prop('disabled', true);
                }
            });
            $('#register-form-ret').on('change', function() {
                $('#register-form-password1').trigger('keyup');
            });
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});