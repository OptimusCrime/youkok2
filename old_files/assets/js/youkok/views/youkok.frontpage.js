var Youkok = (function (module) {
    
    /*
     * Change most popular choice
     */
    var changeMostPopular = function(e) {
        // Default
        e.preventDefault();

        // Get the current module class
        $module_obj = $(this).closest('.frontpage-module');

        // Swap content
        $module_obj.find('.most-popular-label').html($('a', this).html());

        // Swap disabled
        $module_obj.find('li.disabled').removeClass('disabled');
        $(this).addClass('disabled');

        // Get the module id and variable
        var module_id = $module_obj.data('id');
        var module_variable = $module_obj.data('variable');

        // Create the ajax data object
        var ajax_data = {
            module: module_id
        };
        ajax_data[module_variable] = $('a', $(this)).data('delta');

        // Send ajax call
        $.ajax({
            cache: false,
            type: "post",
            url: "processor/module/update",
            data: ajax_data,
            success: function(json) {
                // Check status code
                if (json.code == 200) {
                    // Change was successfull
                    $module_obj.find('.list-group').slideUp(400, function () {
                        // Check for the correct module
                        if (module_id == 1 || module_id == 2) {
                            // Check if actually have any elements to display
                            if (json.data.length > 0) {
                                // Get template
                                var template_frontpage_popular;
                                if (module_id == 1) {
                                    template_frontpage_popular = _.template(
                                        $('script.template-frontpage-popular-elements').html()
                                    );
                                }
                                else {
                                    template_frontpage_popular = _.template(
                                        $('script.template-frontpage-popular-courses').html()
                                    );
                                }
                                
                                // Set content
                                $module_obj.find('.list-group').html(template_frontpage_popular({'elements': json.data}));
                                
                                // Tooltip
                                $module_obj.find('.list-group a').tooltip();
                            }
                            else {
                                if (module_id == 1) {
                                    $module_obj.find('.list-group').html(_.template(
                                        $('script.template-frontpage-no-popular-elements').html()
                                    ));
                                }
                                else {
                                    $module_obj.find('.list-group').html(_.template(
                                        $('script.template-frontpage-no-popular-courses').html()
                                    ));
                                }
                            }
                            
                            // Slide up again
                            $module_obj.find('.list-group').slideDown(400);
                        }
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