var admin_home_graph =  {
    lang: {
        loading: 'Laster...',
        months: ['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'],
        weekdays: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'],
        shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
    },
    chart: {
        renderTo: 'admin-home-graph',
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