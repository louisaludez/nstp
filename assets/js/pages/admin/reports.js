// assets/js/pages/admin/reports.js
const colorPassed = '#10B981';
const colorFailed = '#EF4444';
const colorPending = '#D1D5DB';

const d = window.chartData;

const cwts_pending = d.cwts_total - d.cwts_p - d.cwts_f;
const lts_pending = d.lts_total - d.lts_p - d.lts_f;
const rotc_pending = d.rotc_total - d.rotc_p - d.rotc_f;

const ctxBar = document.getElementById('barChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: ['CWTS', 'LTS', 'ROTC'],
        datasets: [
            { label: 'Passed', data: [d.cwts_p, d.lts_p, d.rotc_p], backgroundColor: colorPassed, borderRadius: 4 },
            { label: 'Failed', data: [d.cwts_f, d.lts_f, d.rotc_f], backgroundColor: colorFailed, borderRadius: 4 },
            { label: 'Pending', data: [cwts_pending, lts_pending, rotc_pending], backgroundColor: colorPending, borderRadius: 4 }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
        scales: {
            y: { beginAtZero: true, stacked: true, grid: { borderDash: [4, 4] }, ticks: { stepSize: 1 } },
            x: { stacked: true, grid: { display: false } }
        }
    }
});

const totalEnrolled = d.cwts_total + d.lts_total + d.rotc_total;
const totalPending = totalEnrolled - d.total_passed - d.total_failed;

const pieData = [d.total_passed, d.total_failed, totalPending];
const pieLabels = [
    'Passed: ' + d.total_passed + ' (' + d.pass_rate + '%)',
    'Failed: ' + d.total_failed + ' (' + d.fail_rate + '%)',
    'Pending: ' + totalPending
];

const ctxPie = document.getElementById('pieChart').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: pieLabels,
        datasets: [{ data: pieData, backgroundColor: [colorPassed, colorFailed, colorPending], borderWidth: 2, borderColor: '#ffffff' }]
    },
    options: { responsive: true, plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } } } }
});
