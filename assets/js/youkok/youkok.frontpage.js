var Youkok = (function (module) {

    /*
     * Public methods
     */
    module.frontpage = {
        
        /*
         * Init the module
         */
        init: function() {
            $('#home-most-popular-dropdown li').on('click', Youkok.frontpage.changeMostPopular);
            $('#home-most-popular-dropdown li').on('click', Youkok.frontpage.changeMostPopular);
            $('.star-remove').on('click', Youkok.frontpage.removeFavorite);
        },
        
        /*
         * Change most popular choice
         */
         changeMostPopular: function(e) {
            // Default
            e.preventDefault();

            // Swap content
            $('#home-most-popular-selected').html($('a', this).html());

            // Swap disabled
            $('#home-most-popular-dropdown li').removeClass('disabled');
            $(this).addClass('disabled');

            // Send ajax call
            $.ajax({
                cache: false,
                type: "post",
                url: "processor/module/update",
                data: {
                    id: 0,
                    delta: $('a', $(this)).data('delta') 
                },
                success: function(json) {
                    // Check status code
                    if (json.code == 200) {
                        // Change was successfull
                        $('#home-most-popular').slideUp(400, function () {
                            $(this).html(json.html).slideDown(400);
                            $('#home-most-popular a').tooltip();
                        });
                    }
                    else {
                        // Something went wrong
                        alert('Noe gikk visst galt her. Ups!');
                    }
                }
            });
         },
         
         /*
          * Remove favorite
          */
         removeFavorite: function() {
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
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});