// assets/js/pages/reports.js
const colorPassed = '#10B981';
const colorFailed = '#EF4444';

const ctxBar = document.getElementById('barChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: ['CWTS', 'LTS', 'ROTC'],
        datasets: [
            { label: 'Passed', data: [window.chartData.cwts_p, window.chartData.lts_p, window.chartData.rotc_p], backgroundColor: colorPassed, borderRadius: 4 },
            { label: 'Failed', data: [window.chartData.cwts_f, window.chartData.lts_f, window.chartData.rotc_f], backgroundColor: colorFailed, borderRadius: 4 }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
        scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4] } }, x: { grid: { display: false } } }
    }
});

const ctxPie = document.getElementById('pieChart').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: [
            'Passed: ' + window.chartData.total_passed + ' (' + window.chartData.pass_rate + '%)',
            'Failed: ' + window.chartData.total_failed + ' (' + window.chartData.fail_rate + '%)'
        ],
        datasets: [{ data: [window.chartData.total_passed, window.chartData.total_failed], backgroundColor: [colorPassed, colorFailed], borderWidth: 2, borderColor: '#ffffff' }]
    },
    options: { responsive: true, plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } } } }
});
