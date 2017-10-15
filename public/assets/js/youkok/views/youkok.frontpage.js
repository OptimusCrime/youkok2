var Youkok = (function (module) {
    
    /*
     * Change most popular choice
     */
    var changeMostPopular = function(e) {
        // Default
        e.preventDefault();

        // Get the current module class
        var $module_obj = $(this).closest('.frontpage-module');

        // Swap content
        $module_obj.find('.most-popular-label').html($('a', this).html());

        // Swap disabled
        $module_obj.find('li.disabled').removeClass('disabled');
        $(this).addClass('disabled');

        var ajax_data = {
            'delta': $(this).find('a').data('delta')
        };

        var template = $module_obj.data('template');
        var template_empty = $module_obj.data('template-empty');

        // Send ajax call
        $.ajax({
            cache: false,
            type: "post",
            url: $module_obj.data('url'),
            data: ajax_data,
            success: function(json) {
                // Check status code
                if (json.code === 200) {
                    // Change was successful
                    $module_obj.find('.list-group').slideUp(400, function () {
                        var template_output = _.template(
                            $(json.data.length > 0 ? template : template_empty).html()
                        );

                        // Set content
                        $module_obj.find('.list-group').html(template_output({'elements': json.data}));

                        // Tooltip
                        $module_obj.find('.list-group a').tooltip();
                            
                        // Slide up again
                        $module_obj.find('.list-group').slideDown(400);
                    });
                }
                else {
                    // Something went wrong
                    alert('Noe gikk visst galt her. Ups!');
                }
            }
        });
    };
     
     /*
      * Remove favorite
      */
     var removeFavorite = function(e) {
        // Avoid window jumping
        e.preventDefault();
        
        // Store reference, just because
        $el = $(this);
        
        // Check if we are currently removing this object
        if (!$el.data('disabled')) {
            // Not removing, add disabled so we can't fire the same event twice
            $el.data('disabled', 'true');
            
            // Set ajax call
            $.ajax({
                cache: false,
                type: "post",
                url: 'processor/favorite',
                data: { 
                    id: $el.data('id'),
                    type: 'remove'
                },
                success: function(json) {
                    // Check status code
                    if (json.code == 200) {
                        // Everything went better than expected :)
                        $el.parent().slideUp(function () {
                            $(this).remove();
                            if ($('#favorites-list li').length == 0) {
                                $('#favorites-list').html('<li class="list-group-item" style="display: none;"><em>Du har ingen favoritter...</em></li>');
                                $('#favorites-list li').slideDown();
                            }
                        });
                        
                        // Display message
                        Youkok.message.add(json.msg);
                    }
                    else {
                        // Something went wrong
                        alert('Noe gikk visst galt her. Ups!');
                        
                        // Remove attribute
                        $el.removeData('disabled');
                    }
                }
            });
        }
    };
    
    /*
     * Public methods
     */
    module.frontpage = {
        
        /*
         * Init the module
         */
        init: function() {
            // Add listeners
            $('.home-most-popular-dropdown li').on('click', changeMostPopular);
            $('.star-remove').on('click', removeFavorite);
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});