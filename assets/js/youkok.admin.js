$(document).ready(function () {
    
    //
    // Init function
    //
    
    function init() {
        //
        // Chart1
        //
        
        var data_dirty1 = jQuery.parseJSON($('#admin-graph .admin-graph-hidden').html());
        var data_clean1 = [];
        for (var i = 0; i < data_dirty1.length; i++) {
            data_clean1.push([eval(data_dirty1[i][0]), parseInt(data_dirty1[i][1], 10)]);
        }
        admin_chart1_options.series[0].data = data_clean1;
        admin_chart1 = new Highcharts.Chart(admin_chart1_options);
        
        //
        // Chart2
        //
        
        var data_dirty2 = jQuery.parseJSON($('#admin-graph-acc .admin-graph-hidden').html());
        var data_clean2 = [];
        for (var i = 0; i < data_dirty2.length; i++) {
            data_clean2.push([eval(data_dirty2[i][0]), parseInt(data_dirty2[i][1], 10)]);
        }
        admin_chart2_options.series[0].data = data_clean2;
        admin_chart2 = new Highcharts.Chart(admin_chart2_options);
    }
    
    //
    // Scripts
    //
    
    $('#admin-scripts a').on('click', function(e) {
        e.preventDefault();
        
        // Clear
        $('#modal-admin-script .modal-body').html('');
        
        // Variables
        var script = 'check_files_404.php';
        var script_data = $(this).data('script');
        
        // Get the correct script
        if (script_data == 'cleaccache') {
            script = 'clear_cache.php';
        }
        else if (script_data == 'loadcourses') {
            script = 'load_courses_ntnu.php';
        }
        else if (script_data == 'updateimages') {
            script = 'update_file_image.php';
        }
        
        // Show modal
        $('#modal-admin-script .modal-body').html('<iframe id="modal-admin-iframe" src="' + $('#search-base').val() + '../scripts/' + script + '"></iframe>');
        $('#modal-admin-script .modal-title').html($(this).text());
        $('#modal-admin-script').modal('show');
    });
    
    //
    // Charts
    //
    
    var admin_chart1 = null;
    var admin_chart1_options =  {
        lang: {
            loading: 'Laster...',
            months: ['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'],
            weekdays: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'],
            shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
        },
        chart: {
            renderTo: 'admin-graph1-display',
            height: 200,
        },
        title: {
            text: '',
            style: {
                display: 'none'
            }
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                month: '%e. %b',
                year: '%b'
            },
        },
        yAxis: {
            min: 0,
            title: {
                enabled: false,
            },
        },
        tooltip: {
            headerFormat: '<b>Antall nedlastninger</b><br>',
            pointFormat: '{point.x:%e. %b %Y}: {point.y:.0f}'
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        series: [{
            name: 'Populæritet',
            data: [],
        }]
    };
    var admin_chart2 = null;
    var admin_chart2_options =  {
        lang: {
            loading: 'Laster...',
            months: ['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'],
            weekdays: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'],
            shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
        },
        chart: {
            renderTo: 'admin-graph2-display',
            height: 200,
        },
        title: {
            text: '',
            style: {
                display: 'none'
            }
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                month: '%e. %b',
                year: '%b'
            },
        },
        yAxis: {
            min: 0,
            title: {
                enabled: false,
            },
        },
        tooltip: {
            headerFormat: '<b>Antall totale nedlastninger</b><br>',
            pointFormat: '{point.x:%e. %b %Y}: {point.y:.0f}'
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        series: [{
            name: 'Populæritet',
            data: [],
        }]
    };
    
    //
    // Load
    //
    
    init();
    
});