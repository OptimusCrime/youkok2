var Youkok = (function (module) {
    
    // Template
    var template  = '<div class="time">';
        template += '    <span class="count curr top">00</span>';
        template += '    <span class="count next top">00</span>';
        template += '    <span class="count next bottom">00</span>';
        template += '    <span class="count curr bottom">00</span>';
        template += '    <span class="label"></span>';
        template += '</div>';
    
    // Stuff
    var labels = ['dager', 'timer', 'minutter', 'sekunder'],
        currDate = '00:00:00:00',
        nextDate = '00:00:00:00',
        $countdown_container = $('.countdown-wrapper');
    
    /*
     * Private methods
     */
    
    // Parse countdown string to an object
    function strfobj(str) {
        var parsed = str.split(':'),
        obj = {};
        labels.forEach(function(label, i) {
            obj[label] = parsed[i];
        });
        return obj;
    }
    
    // Return the time components that diffs
    function diff(obj1, obj2) {
        var diff = [];
        labels.forEach(function(key) {
            if (obj1[key] !== obj2[key]) {
                diff.push(key);
            }
        });
        return diff;
    }
    
    /*
     * Public methods
     */
    module.countdown = {
        
        /*
         * Init function that starts the countdown plugin
         */
        init: function () {
            // Build the layout
            var initData = strfobj(currDate);
                labels.forEach(function(label, i) {
                    var $section = $($.parseHTML(template));
                    $section.addClass(label);
                    $section.find('.label').html(label);
                    $countdown_container.append($section);
            });
            
            // Starts the countdown
            $countdown_container.countdown($countdown_container.data('exam'), function(event) {
                var newDate = event.strftime('%D:%H:%M:%S'),
                    data;
                
                if (newDate !== nextDate) {
                    currDate = nextDate;
                    nextDate = newDate;
                    // Setup the data
                    data = {
                        'curr': strfobj(currDate),
                        'next': strfobj(nextDate)
                    };
                    
                    // Apply the new values to each node that changed
                    diff(data.curr, data.next).forEach(function(label) {
                        var selector = '.%s'.replace(/%s/, label),
                            $node = $countdown_container.find(selector);
                        
                        // Update the node
                        $node.removeClass('flip');
                        $node.find('.curr').text(data.curr[label]);
                        $node.find('.next').text(data.next[label]);
                        
                        // Wait for a repaint to then flip
                        setTimeout(function($node) {
                            $node.addClass('flip');
                        }, 50, $node);
                    });
                }
            });
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});