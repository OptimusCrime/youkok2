import Highcharts from 'highcharts';

export const graph = {
  chart: {
    type: 'line',
    height: 200,
  },
  title: null,
  yAxis: {
    title: null,
  },
  xAxis: {
    type: 'datetime',
  },
  tooltip: {
    headerFormat: '<b>Antall totale nedlastninger</b><br>',
    pointFormat: '{point.x:%A %e. %b %Y}: {point.y:.0f}'
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
