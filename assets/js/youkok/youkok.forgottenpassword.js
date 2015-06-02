var Youkok = (function (module) {

    /*
     * Public methods
     */
    module.forgottenPassword
    = {
        
        /*
         * Init the module
         */
        init: function () {
            $('#forgotten-password-new-form input').on('keyup', function () {
                var $that = $(this);
                var $that_parent = $that.parent();
                var element_id = this.id;

                if (this.id == 'forgotten-password-new-form-password1') {
                    if ($that.val().length < 7) {
                        if (!$that_parent.hasClass('has-error')) {
                            $that_parent.addClass('has-error');
                        }
                        $('#forgotten-password-new-form-password-error1').css('color', 'red');
                        $('#forgotten-password-new-form-password2').prop('disabled', true).val('').parent().removeClass('has-error');
                        $('#forgotten-password-new-form-password-error2').css('color', '');
                    }
                    else {
                        if ($that_parent.hasClass('has-error')) {
                            $that_parent.removeClass('has-error');
                        }
                        $('#forgotten-password-new-form-password-error1').css('color', '');
                        $('#forgotten-password-new-form-password2').prop('disabled', false);
                    }

                    if ($('#forgotten-password-new-form-password2').val().length != 0) {
                        if ($('#forgotten-password-new-form-password1').val() != $('#forgotten-password-new-form-password2').val()) {
                            if (!$('#forgotten-password-new-form-password2').parent().hasClass('has-error')) {
                                $('#forgotten-password-new-form-password2').parent().addClass('has-error');
                            }
                            $('#forgotten-password-new-form-password-error2').css('color', 'red');
                        }
                        else {
                            if ($('#forgotten-password-new-form-password2').parent().hasClass('has-error')) {
                                $('#forgotten-password-new-form-password2').parent().removeClass('has-error');
                            }
                            $('#forgotten-password-new-form-password-error2').css('color', '');
                        }
                    }
                }
                else {
                    if ($('#forgotten-password-new-form-password1').val() != $('#forgotten-password-new-form-password2').val()) {
                        if (!$that_parent.hasClass('has-error')) {
                            $that_parent.addClass('has-error');
                        }
                        $('#forgotten-password-new-form-password-error2').css('color', 'red');
                    }
                    else {
                        if ($that_parent.hasClass('has-error')) {
                            $that_parent.removeClass('has-error');
                        }
                        $('#forgotten-password-new-form-password-error2').css('color', '');
                    }
                }

                if ($('#forgotten-password-new-form .has-error').length == 0 && $('#forgotten-password-new-form-password1').val().length > 6) {
                    $('#forgotten-password-new-form-submit').prop('disabled', false);
                }
                else {
                    $('#forgotten-password-new-form-submit').prop('disabled', true);
                }
            });
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});