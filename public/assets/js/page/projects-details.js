"use strict";

Dropzone.autoDiscover = false;
var dropzone = new Dropzone("#mydropzone", {
  url: base_url+"projects/upload-files/"+project_id
});

dropzone.on("complete", function(file) {
  $('#file_list').bootstrapTable('refresh');
});

// var ctx = document.getElementById("project_statistics").getContext('2d');
// var myChart = new Chart(ctx, {
//   type: 'bar',
//   data: {
//     labels: JSON.parse(task_status),
//     datasets: [{
//       data: JSON.parse(task_status_values),
//       borderWidth: 2,
//       backgroundColor: [
//         '#fc544b',
//         '#ffa426',
//         '#3abaf4',
//         '#63ed7a',
//       ]
//     }]
//   },
//   options: {
//     legend: {
//       display: false
//     },
//     scales: {
//       yAxes: [{
//         gridLines: {
//           drawBorder: false,
//           color: '#f2f2f2',
//         },
//         ticks: {
//           beginAtZero: true,
//           stepSize: 1
//         }
//       }],
//       xAxes: [{
//         gridLines: {
//           display: false
//         }
//       }]
//     },
//   }
// });



// var myChartCircle = new Chart('project_progress', {
//   type: 'doughnut',
//   data: {
//     datasets: [{
//       label: progress,
//       percent: progres_count,
//       backgroundColor: [theme_color]
//     }]
//   },
//   plugins: [{
//       beforeInit: (chart) => {
//         const dataset = chart.data.datasets[0];
//         chart.data.labels = [dataset.label];
//         dataset.data = [dataset.percent, 100 - dataset.percent];
//       }
//     },
//     {
//       beforeDraw: (chart) => {
//         var width = chart.chart.width,
//           height = chart.chart.height,
//           ctx = chart.chart.ctx;
//         ctx.restore();
//         var fontSize = (height / 150).toFixed(2);
//         ctx.font = fontSize + "em sans-serif";
//         ctx.fillStyle = "#9b9b9b";
//         ctx.textBaseline = "middle";
//         var text = chart.data.datasets[0].percent + "%",
//           textX = Math.round((width - ctx.measureText(text).width) / 2),
//           textY = height / 2;
//         ctx.fillText(text, textX, textY);
//         ctx.save();
//       }
//     }
//   ],
//   options: {
//     maintainAspectRatio: true,
//     cutoutPercentage: 85,
//     rotation: Math.PI / 2,
//     legend: {
//       display: false,
//     },
//     tooltips: {
//       filter: tooltipItem => tooltipItem.index == 0
//     }
//   }
// });

