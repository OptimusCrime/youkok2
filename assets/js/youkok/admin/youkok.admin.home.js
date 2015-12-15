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
            url: 'processor/admin/homeboxes',
            success: function(json) {
                if (json.code == 200) {
                    $('#admin-home-downloads').html(json.data.downloads);
                    $('#admin-home-users').html(json.data.users);
                    $('#admin-home-files').html(json.data.files);
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
            url: 'processor/admin/homegraph',
            success: function(json) {
                if (json.code == 200) {
                    // Display the delta
                    $('#admin-home-graph-delta').html(json.data.delta);
                    
                    // Parse the plots
                    var admin_home_graph_series = [];
                    for (var i = 0; i < json.data.graph.length; i++) {
                        var plot_date = json.data.graph[i].date.split('-');
                        admin_home_graph_series.push([Date.UTC(parseInt(plot_date[0]), parseInt(plot_date[1]) - 1, parseInt(plot_date[2]), 0, 0, 0), json.data.graph[i].downloads]);
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