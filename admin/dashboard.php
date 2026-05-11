<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require 'controllers/DashboardController.php';

$extra_css = [
    '../assets/css/pages/admin-dashboard.css',
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
];
include '../includes/header.php';
include '../includes/admin_sidebar.php';

// Current date info for calendar
$today = new DateTime();
$user_name_first = explode(' ', $_SESSION['full_name'] ?? 'Admin')[0];
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">

    <?php include '../includes/topbar.php'; ?>

    <!-- Page Greeting -->
    <div class="dash-greeting mb-4">
        <h3>Hi, <?= htmlspecialchars($user_name_first) ?> 👋</h3>
        <p>Welcome back! Here's what's happening with NSTP today.</p>
    </div>

    <!-- ═══════════════════════════════════════
         ROW 1 — Stat Cards (3 columns)
    ═══════════════════════════════════════ -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="dash-stat-card">
                <div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-value"><?= number_format($total_students) ?></div>
                    <div class="stat-sub">Enrolled this semester</div>
                </div>
                <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="dash-stat-card">
                <div>
                    <div class="stat-label">Active Instructors</div>
                    <div class="stat-value"><?= number_format($active_instructors) ?></div>
                    <div class="stat-sub">Across all components</div>
                </div>
                <div class="stat-icon green"><i class="bi bi-person-check-fill"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="dash-stat-card">
                <div>
                    <div class="stat-label">Certificates Issued</div>
                    <div class="stat-value"><?= number_format($certificates_issued) ?></div>
                    <div class="stat-sub">Total awarded</div>
                </div>
                <div class="stat-icon purple"><i class="bi bi-award-fill"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="dash-stat-card">
                <div>
                    <div class="stat-label">Passed Students</div>
                    <div class="stat-value"><?= number_format($passed_students) ?></div>
                    <div class="stat-sub">Successfully completed</div>
                </div>
                <div class="stat-icon indigo"><i class="bi bi-check-circle-fill"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="dash-stat-card">
                <div>
                    <div class="stat-label">Failed Students</div>
                    <div class="stat-value"><?= number_format($failed_students) ?></div>
                    <div class="stat-sub neg">Did not pass</div>
                </div>
                <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="dash-stat-card">
                <div>
                    <div class="stat-label">Upcoming Activities</div>
                    <div class="stat-value"><?= number_format($upcoming_activities) ?></div>
                    <div class="stat-sub">Scheduled ahead</div>
                </div>
                <div class="stat-icon orange"><i class="bi bi-calendar-event-fill"></i></div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
         ROW 2 — Chart | Activity | Calendar
    ═══════════════════════════════════════ -->
    <div class="row g-3">

        <!-- Component Distribution + Chart (col 8) -->
        <div class="col-lg-8">
            <div class="row g-3 h-100">

                <!-- Component Distribution progress bars -->
                <div class="col-12">
                    <div class="dash-panel">
                        <div class="panel-title">📊 Component Distribution</div>
                        <div class="chart-wrapper" style="height: 220px; display: flex; justify-content: center;">
                            <canvas id="componentPieChart"></canvas>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-4 mt-3 mb-3">
                            <div class="text-center">
                                <span style="display:inline-block;width:10px;height:10px;background:#6366F1;border-radius:50%;margin-right:4px;"></span>CWTS<br>
                                <small class="text-muted fw-bold"><?= $cwts_percent ?>%</small>
                            </div>
                            <div class="text-center">
                                <span style="display:inline-block;width:10px;height:10px;background:#10B981;border-radius:50%;margin-right:4px;"></span>LTS<br>
                                <small class="text-muted fw-bold"><?= $lts_percent ?>%</small>
                            </div>
                            <div class="text-center">
                                <span style="display:inline-block;width:10px;height:10px;background:#F97316;border-radius:50%;margin-right:4px;"></span>ROTC<br>
                                <small class="text-muted fw-bold"><?= $rotc_percent ?>%</small>
                            </div>
                        </div>

                        <!-- Quick summary pills -->
                        <div class="quick-stat-row">
                            <div class="quick-stat-pill">
                                <div class="qsp-val"><?= $total_students > 0 ? round(($passed_students/$total_students)*100) : 0 ?>%</div>
                                <div class="qsp-lbl">Pass Rate</div>
                            </div>
                            <div class="quick-stat-pill">
                                <div class="qsp-val"><?= $total_students > 0 ? round(($failed_students/$total_students)*100) : 0 ?>%</div>
                                <div class="qsp-lbl">Fail Rate</div>
                            </div>
                            <div class="quick-stat-pill">
                                <div class="qsp-val"><?= $total_students > 0 ? round(($certificates_issued/$total_students)*100) : 0 ?>%</div>
                                <div class="qsp-lbl">Cert. Rate</div>
                            </div>
                            <div class="quick-stat-pill">
                                <div class="qsp-val"><?= $upcoming_activities ?></div>
                                <div class="qsp-lbl">Upcoming</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bar chart -->
                <div class="col-12">
                    <div class="dash-panel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="panel-title mb-0">📈 Student Distribution Chart</div>
                            <div class="d-flex gap-2">
                                <span class="badge-chip default"><i class="bi bi-circle-fill" style="font-size:6px;color:#6366F1"></i> CWTS</span>
                                <span class="badge-chip create"><i class="bi bi-circle-fill" style="font-size:6px;color:#10B981"></i> LTS</span>
                                <span class="badge-chip update"><i class="bi bi-circle-fill" style="font-size:6px;color:#F97316"></i> ROTC</span>
                            </div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="componentChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right column: Calendar + Activity Feed (col 4) -->
        <div class="col-lg-4">
            <div class="row g-3 h-100">

                <!-- Mini Calendar -->
                <div class="col-12">
                    <div class="dash-panel">
                        <div class="mini-calendar" id="miniCal">
                            <div class="cal-header">
                                <button class="cal-nav" id="calPrev"><i class="bi bi-chevron-left"></i></button>
                                <h6 id="calTitle"></h6>
                                <button class="cal-nav" id="calNext"><i class="bi bi-chevron-right"></i></button>
                            </div>
                            <table id="calTable">
                                <thead>
                                    <tr>
                                        <th>MON</th><th>TUE</th><th>WED</th>
                                        <th>THU</th><th>FRI</th><th>SAT</th><th>SUN</th>
                                    </tr>
                                </thead>
                                <tbody id="calBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div class="col-12">
                    <div class="dash-panel" style="overflow-y:auto; max-height: 340px;">
                        <div class="panel-title">🕐 Recent Activity</div>
                        <?php if (count($recent_activities) > 0): ?>
                        <ul class="activity-feed">
                            <?php foreach ($recent_activities as $activity):
                                $type = strtolower($activity['action_type'] ?? '');
                                if (str_contains($type, 'delete')) { $dot = 'd'; $chip = 'delete'; $chipLabel = 'Delete'; }
                                elseif (str_contains($type, 'update')) { $dot = 'u'; $chip = 'update'; $chipLabel = 'Update'; }
                                elseif (str_contains($type, 'create') || str_contains($type, 'add')) { $dot = 'c'; $chip = 'create'; $chipLabel = 'Create'; }
                                else { $dot = 'ok'; $chip = 'default'; $chipLabel = 'Action'; }
                            ?>
                            <li class="activity-item">
                                <div class="activity-dot <?= $dot ?>"></div>
                                <div class="activity-text">
                                    <p><?= htmlspecialchars($activity['action_type']) ?></p>
                                    <small><?= htmlspecialchars($activity['details'] ?? '') ?></small>
                                    <small style="margin-top:2px;">
                                        <span class="badge-chip <?= $chip ?>"><?= $chipLabel ?></span>
                                        &nbsp;by <?= htmlspecialchars($activity['user_name'] ?? 'System') ?>
                                    </small>
                                </div>
                                <div class="activity-time"><?= time_elapsed_string($activity['created_at'] ?? null) ?></div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                            <p class="text-muted small text-center py-4">No recent activity found.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </div><!-- /row 2 -->

</div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ─── Mini Calendar ───────────────────────────────────────
(function() {
    const MONTHS = ['January','February','March','April','May','June',
                    'July','August','September','October','November','December'];
    const now = new Date();
    let cur = new Date(now.getFullYear(), now.getMonth(), 1);

    function buildCal(base) {
        const y = base.getFullYear(), m = base.getMonth();
        document.getElementById('calTitle').textContent = MONTHS[m] + ' ' + y;
        const body = document.getElementById('calBody');
        body.innerHTML = '';
        // Start from Monday
        let start = new Date(y, m, 1);
        let dow = start.getDay(); // 0=Sun
        let offset = (dow === 0) ? 6 : dow - 1;
        const days = new Date(y, m+1, 0).getDate();
        let cells = [];
        for (let i = 0; i < offset; i++) cells.push(null);
        for (let d = 1; d <= days; d++) cells.push(d);
        while (cells.length % 7 !== 0) cells.push(null);
        for (let r = 0; r < cells.length/7; r++) {
            const tr = document.createElement('tr');
            for (let c = 0; c < 7; c++) {
                const td = document.createElement('td');
                const d = cells[r*7+c];
                if (d) {
                    td.textContent = d;
                    if (y===now.getFullYear() && m===now.getMonth() && d===now.getDate())
                        td.classList.add('today');
                } else {
                    td.textContent = '';
                    td.classList.add('other-month');
                }
                tr.appendChild(td);
            }
            body.appendChild(tr);
        }
    }
    buildCal(cur);
    document.getElementById('calPrev').onclick = () => { cur.setMonth(cur.getMonth()-1); buildCal(cur); };
    document.getElementById('calNext').onclick = () => { cur.setMonth(cur.getMonth()+1); buildCal(cur); };
})();

// ─── Bar Chart ────────────────────────────────────────────
(function() {
    const ctx = document.getElementById('componentChart').getContext('2d');
    const labels = ['CWTS', 'LTS', 'ROTC'];
    const data   = [<?= $cwts_count ?>, <?= $lts_count ?>, <?= $rotc_count ?>];
    const colors = ['#6366F1','#10B981','#F97316'];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Students',
                data: data,
                backgroundColor: colors.map(c => c + '33'),
                borderColor: colors,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y.toLocaleString() + ' students'
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { font: { family: 'Inter', size: 12 }, color: '#9CA3AF' }
                },
                y: {
                    grid: { color: '#F3F4F6' },
                    border: { display: false, dash: [4,4] },
                    ticks: { font: { family: 'Inter', size: 11 }, color: '#9CA3AF' },
                    beginAtZero: true
                }
            }
        }
    });
})();

// ─── Pie Chart (Component Distribution) ──────────────────────
(function() {
    const ctxPie = document.getElementById('componentPieChart').getContext('2d');
    const dataPie   = [<?= $cwts_count ?>, <?= $lts_count ?>, <?= $rotc_count ?>];
    const colorsPie = ['#6366F1', '#10B981', '#F97316'];

    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['CWTS', 'LTS', 'ROTC'],
            datasets: [{
                data: dataPie,
                backgroundColor: colorsPie,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y.toLocaleString() + ' students'
                    }
                }
            },
            layout: {
                padding: 10
            }
        }
    });
})();
</script>
</body>
</html>
