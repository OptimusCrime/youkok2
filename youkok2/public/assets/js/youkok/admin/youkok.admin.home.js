/*
 * The Youkok2 admin module
 */

var Youkok = (function (module) {
    
    // Make sure we have a admin module
    module.admin = module.admin || {};
    
    /*
     * Load the information in the top boxes
     */
    
    var initBoxes = function() {
        // Call once
        fetchBoxes();
        
        // Start interval 30s
        setInterval(fetchBoxes, 30000);
    };
    var fetchBoxes = function () {
        $.ajax({
            cache: false,
            url: $('#admin-homeboxes').data('url'),
            success: function(json) {
                if (json.code == 200) {
                    $('#admin-home-downloads').html(json.data.downloads);
                    $('#admin-home-sessions').html(json.data.sessions_day + ' (' + json.data.sessions_week + ')');
                    $('#admin-home-elements').html(json.data.elements);
                    $('#admin-home-courses').html(json.data.courses);
                }
            }
        });
    };
    
    /*
     * Load the graph with data
     */
    
    var initGraph = function () {
         $.ajax({
            cache: false,
            url: $('#admin-home-graph').data('url'),
            success: function(json) {
                if (json.code == 200) {
                    
                    // Parse the plots
                    var admin_home_graph_series = [];
                    for (var i = 0; i < json.data.length; i++) {
                        var plot_date = json.data[i].date.split('-');
                        admin_home_graph_series.push([Date.UTC(parseInt(plot_date[0]), parseInt(plot_date[1]) - 1, parseInt(plot_date[2]), 0, 0, 0), json.data[i].downloads]);
                    }
                    admin_home_graph.series[0].data = admin_home_graph_series;
                    
                    // Create the graph
                    new Highcharts.Chart(admin_home_graph);
                }
            }
        });
    };
    
    /*
     * Public methods
     */
    
    module.admin.home = {
        
        /*
         * Init the subview
         */
        
        init: function() {
            // Init the modules
            initBoxes();
            initGraph();
        }
    };
    
    /*
     * Return the module
     */
    
    return module;
})(Youkok || {});