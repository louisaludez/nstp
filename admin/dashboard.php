<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/DashboardController.php';

if (isset($_POST['simulate_ched'])) {
    // Send notification to all Instructors and ROTC
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, role, message, link) SELECT id, role, 'CHED has sent an official Email Notification regarding NSTP requirements.', '#' FROM users WHERE role IN ('Instructor', 'ROTC')");
        $stmt->execute();
        $_SESSION['success'] = "CHED Email Notification successfully sent to all Instructors and ROTC officers.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to simulate email: " . $e->getMessage();
    }
    header("Location: dashboard.php");
    exit;
}
$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';

$user_name_first = explode(' ', $_SESSION['full_name'] ?? 'Admin')[0];
$semester_label = 'Spring Semester · ' . date('Y');

// Calculate pass rate
$pass_rate = $total_students > 0 ? round(($passed_students / $total_students) * 100, 1) : 0;

// Sections count
try {
    $sec_count_stmt = $pdo->query("SELECT COUNT(*) FROM sections");
    $active_sections = $sec_count_stmt->fetchColumn() ?: 0;
} catch (Exception $e) {
    $active_sections = 0;
}

// Fetch upcoming activities for calendar + recent list
$upcoming_list = [];
try {
    $act_stmt = $pdo->query("SELECT title, activity_date, status FROM activities WHERE activity_date >= CURDATE() ORDER BY activity_date ASC LIMIT 10");
    $upcoming_list = $act_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}

// Calendar helper: get activity dates for highlighting
$activity_dates = [];
foreach ($upcoming_list as $act) {
    $activity_dates[] = date('j', strtotime($act['activity_date']));
}
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">

    <?php include '../includes/topbar.php'; ?>

    <!-- Simulate CHED Email Action -->
    <div class="d-flex justify-content-end mb-3 mt-2">
        <form method="POST" action="dashboard.php" class="needs-confirmation" data-confirm="Are you sure you want to broadcast a simulated CHED Email Notification to all faculty?" data-btn="Yes, send it" data-icon="question">
            <button type="submit" name="simulate_ched" class="btn btn-sm text-white px-3" style="background: #10B981; border-radius: 8px; font-weight: 500;">
                <i class="bi bi-envelope me-1"></i> Simulate CHED Email
            </button>
        </form>
    </div>

    <!-- ═══════════════════════════════════════
         ROW 1 — 5 Stat Cards
    ═══════════════════════════════════════ -->
    <div class="row g-3 mb-4 mt-1">
        <div class="col-sm-6 col-lg-4 col-xl">
            <div class="stat-card-figma">
                <div class="stat-icon coral"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg></div>
                <div class="stat-body">
                    <div class="stat-value"><?= number_format($total_students) ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <span class="stat-delta up">+4.2%</span>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl">
            <div class="stat-card-figma">
                <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                    </svg></div>
                <div class="stat-body">
                    <div class="stat-value"><?= number_format($active_sections) ?></div>
                    <div class="stat-label">Active Sections</div>
                </div>
                <span class="stat-delta up">+<?= $active_sections ?></span>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl">
            <div class="stat-card-figma">
                <div class="stat-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                        <polyline points="17 6 23 6 23 12" />
                    </svg></div>
                <div class="stat-body">
                    <div class="stat-value"><?= $pass_rate ?>%</div>
                    <div class="stat-label">Pass Rate</div>
                </div>
                <span class="stat-delta up">+1.8%</span>
            </div>
        </div>
        <div class="col-sm-6 col-lg-6 col-xl">
            <div class="stat-card-figma">
                <div class="stat-icon indigo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="width: 20px; height: 20px;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <polyline points="9 15 11 17 15 13" />
                    </svg></div>
                <div class="stat-body">
                    <div class="stat-value"><?= number_format($upcoming_activities) ?></div>
                    <div class="stat-label">Reports Pending</div>
                </div>
                <span class="stat-delta down">-<?= $upcoming_activities ?></span>
            </div>
        </div>
        <div class="col-sm-12 col-lg-6 col-xl">
            <div class="stat-card-figma position-relative">
                <div class="stat-icon" style="background-color: #EF4444; color: white;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg></div>
                <div class="stat-body">
                    <div class="stat-value"><?= number_format($flagged_count) ?></div>
                    <div class="stat-label">Incomplete Profiles</div>
                </div>
                <?php if ($flagged_count === 0): ?>
                    <span class="position-absolute" style="top: 16px; right: 16px; background: #ECFDF5; color: #10B981; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Complete</span>
                <?php else: ?>
                    <span class="position-absolute" style="top: 16px; right: 16px; background: #FEF2F2; color: #EF4444; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Action Needed</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
         ROW 2 — NSTP Component Chart + Calendar of Activities
         (matching reference layout: col-8 chart + col-4 calendar/activities)
    ═══════════════════════════════════════ -->
    <div class="row g-3 mb-4">

        <!-- NSTP Component Chart (Line Chart) -->
        <div class="col-lg-8">
            <div class="dash-panel">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <div class="panel-title mb-0">NSTP Component</div>
                        <div style="font-size:0.8rem;color:#6B7280;">Total students enrolled per semester</div>
                    </div>
                    <button class="btn btn-sm border-0 text-muted"><svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="width: 16px; height: 16px;">
                            <circle cx="12" cy="12" r="1" />
                            <circle cx="19" cy="12" r="1" />
                            <circle cx="5" cy="12" r="1" />
                        </svg></button>
                </div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span
                        style="font-size:1.75rem;font-weight:700;color:#111827;"><?= number_format($total_students) ?></span>
                    <span class="stat-delta up" style="font-size:0.75rem;">▲ 4.2% vs last semester</span>
                </div>
                <div class="chart-wrapper" style="height:220px;">
                    <canvas id="enrollmentTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Calendar of Activities (matching reference right panel) -->
        <div class="col-lg-4">
            <div class="dash-panel" style="padding:20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="panel-title mb-0" style="font-size:0.95rem;">Calendar of Activities</div>
                        <div style="font-size:0.78rem;color:#6B7280;"><?= date('F Y') ?></div>
                    </div>
                    <button class="btn btn-sm border-0" style="background:#EEF2FF;border-radius:6px;padding:4px 8px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            style="width: 16px; height: 16px; color:#6366F1;">
                            <rect width="18" height="18" x="3" y="4" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </button>
                </div>
                <!-- Mini Calendar -->
                <div id="miniCalendar" style="font-size:0.78rem;"></div>
                <div class="d-flex gap-3 mt-3" style="font-size:0.72rem;color:#6B7280;">
                    <span><span
                            style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#6366F1;margin-right:4px;"></span>
                        Today</span>
                    <span><span
                            style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#D1D5DB;margin-right:4px;"></span>
                        Has activity</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
         ROW 3 — Pass/Fail Chart + Recent Activities
         (matching reference layout)
    ═══════════════════════════════════════ -->
    <div class="row g-3 mb-4">

        <!-- Pass/Fail by Program (Bar Chart) -->
        <div class="col-lg-8">
            <div class="dash-panel">
                <div class="panel-title">Pass / Fail by Program</div>
                <div style="font-size:0.8rem;color:#6B7280;margin-top:-12px;margin-bottom:12px;">Current semester
                    outcomes</div>
                <div class="d-flex gap-3 mb-3">
                    <span style="font-size:0.75rem;color:#6B7280;"><span
                            style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#10B981;margin-right:4px;"></span>
                        Passed</span>
                    <span style="font-size:0.75rem;color:#6B7280;"><span
                            style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#EF4444;margin-right:4px;"></span>
                        Failed</span>
                </div>
                <div class="chart-wrapper" style="height:260px;">
                    <canvas id="passFailChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities (matching reference right panel) -->
        <div class="col-lg-4">
            <div class="dash-panel" style="padding:20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="panel-title mb-0" style="font-size:0.95rem;">Recent Activities</div>
                        <div style="font-size:0.78rem;color:#6B7280;">Latest scheduled events</div>
                    </div>
                    <button class="btn btn-sm border-0" style="background:#ECFDF5;border-radius:6px;padding:4px 8px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            style="width: 16px; height: 16px; color:#10B981;">
                            <rect width="18" height="18" x="3" y="4" rx="2" />
                            <path d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" />
                        </svg>
                    </button>
                </div>
                <div class="d-flex flex-column gap-2">
                    <?php if (empty($upcoming_list)): ?>
                        <div style="font-size:0.82rem;color:#9CA3AF;text-align:center;padding:20px 0;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                style="width: 24px; height: 24px; display:block; margin: 0 auto 8px; opacity: 0.6;">
                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                                <line x1="10" y1="14" x2="14" y2="18" />
                                <line x1="14" y1="14" x2="10" y2="18" />
                            </svg>
                            No upcoming activities
                        </div>
                    <?php else: ?>
                        <?php
                        $activity_colors = ['#6366F1', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
                        foreach (array_slice($upcoming_list, 0, 4) as $idx => $activity):
                            $dot_color = $activity_colors[$idx % count($activity_colors)];
                            $status = $activity['status'] ?? 'Draft';
                            $status_bg = $status === 'Submitted' ? '#ECFDF5' : ($status === 'Approved' ? '#EEF2FF' : '#FEF3C7');
                            $status_color = $status === 'Submitted' ? '#10B981' : ($status === 'Approved' ? '#6366F1' : '#F59E0B');
                            ?>
                            <div class="d-flex align-items-start gap-3 py-2" style="border-bottom:1px solid #F3F4F6;">
                                <div
                                    style="width:8px;height:8px;border-radius:50%;background:<?= $dot_color ?>;margin-top:6px;flex-shrink:0;">
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:0.85rem;font-weight:600;color:#111827;line-height:1.3;">
                                        <?= htmlspecialchars($activity['title']) ?>
                                    </div>
                                    <div style="font-size:0.72rem;color:#6B7280;margin-top:2px;">
                                        <?= date('M j, Y', strtotime($activity['activity_date'])) ?> ·
                                        <?= date('g:i A', strtotime($activity['activity_date'])) ?>
                                    </div>
                                </div>
                                <span
                                    style="font-size:0.65rem;font-weight:600;padding:2px 8px;border-radius:10px;background:<?= $status_bg ?>;color:<?= $status_color ?>;white-space:nowrap;">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
         ROW 4 — Incomplete Student Profiles
    ═══════════════════════════════════════ -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dash-panel" style="padding:24px;">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 style="font-size: 1.15rem; font-weight: 700; color: #111827; margin-bottom: 4px;">Incomplete Student Profiles</h5>
                        <p style="font-size: 0.85rem; color: #6B7280; margin: 0;">Flags missing information in archived records</p>
                    </div>
                    <span style="background: #D1FAE5; color: #065F46; font-size: 0.8rem; font-weight: 600; padding: 4px 12px; border-radius: 12px;">
                        <?= $flagged_count ?> Flagged
                    </span>
                </div>

                <div class="position-relative mb-4">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #9CA3AF;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" class="form-control" placeholder="Search flagged students..." style="padding-left: 36px; border-radius: 8px; font-size: 0.9rem; border: 1px solid #E5E7EB; box-shadow: none;">
                </div>

                <?php if ($flagged_count === 0): ?>
                    <div class="text-center py-5">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: #ECFDF5; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px; color: #10B981;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <h6 style="font-size: 1.05rem; font-weight: 600; color: #111827; margin-bottom: 8px;">All Profiles Complete</h6>
                        <p style="font-size: 0.85rem; color: #9CA3AF; margin: 0;">All archived students have complete data fields.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-figma">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Missing Fields</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($flagged_students as $student): 
                                    $missing = [];
                                    if (empty($student['email'])) $missing[] = 'Email';
                                    if (empty($student['contact_number'])) $missing[] = 'Contact';
                                    if (empty($student['course'])) $missing[] = 'Course';
                                ?>
                                    <tr>
                                        <td><span class="fw-600"><?= htmlspecialchars($student['student_id']) ?></span></td>
                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                        <td><?= htmlspecialchars($student['course'] ?: '—') ?></td>
                                        <td>
                                            <?php foreach ($missing as $m): ?>
                                                <span class="badge-chip delete me-1"><?= $m ?></span>
                                            <?php endforeach; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="manage_students.php?search=<?= urlencode($student['student_id']) ?>" class="btn btn-sm btn-figma outline">Update</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>

    // ─── Mini Calendar (matching reference design) ──────────────
    (function () {
        const container = document.getElementById('miniCalendar');
        if (!container) return;

        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth();
        const today = now.getDate();
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Activity dates from PHP
        const activityDays = [<?= implode(',', $activity_dates) ?>];

        let html = '<table style="width:100%;border-collapse:collapse;text-align:center;">';
        html += '<tr>';
        ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'].forEach(d => {
            html += `<th style="padding:6px 2px;font-size:0.68rem;font-weight:600;color:#9CA3AF;text-transform:uppercase;">${d}</th>`;
        });
        html += '</tr>';

        let dayCount = 1;
        for (let row = 0; row < 6 && dayCount <= daysInMonth; row++) {
            html += '<tr>';
            for (let col = 0; col < 7; col++) {
                if (row === 0 && col < firstDay || dayCount > daysInMonth) {
                    html += '<td style="padding:4px;"></td>';
                } else {
                    const isToday = dayCount === today;
                    const hasActivity = activityDays.includes(dayCount);
                    let cellStyle = 'padding:4px;font-size:0.78rem;cursor:pointer;';
                    let numStyle = 'display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;';

                    if (isToday) {
                        numStyle += 'background:#6366F1;color:#fff;font-weight:700;';
                    } else if (hasActivity) {
                        numStyle += 'background:#EEF2FF;color:#6366F1;font-weight:600;';
                    } else {
                        numStyle += 'color:#374151;';
                    }

                    html += `<td style="${cellStyle}"><span style="${numStyle}">${dayCount}</span></td>`;
                    dayCount++;
                }
            }
            html += '</tr>';
        }
        html += '</table>';
        container.innerHTML = html;
    })();

    // ─── NSTP Component (Line Chart) ──────────────────────
    (function () {
        const ctx = document.getElementById('enrollmentTrendChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(99,102,241,0.15)');
        gradient.addColorStop(1, 'rgba(99,102,241,0.01)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
                datasets: [{
                    data: [
                        <?= max(1, $total_students - 600) ?>,
                        <?= max(1, $total_students - 500) ?>,
                        <?= max(1, $total_students - 400) ?>,
                        <?= max(1, $total_students - 300) ?>,
                        <?= max(1, $total_students - 250) ?>,
                        <?= max(1, $total_students - 150) ?>,
                        <?= max(1, $total_students - 50) ?>,
                        <?= $total_students ?>
                    ],
                    borderColor: '#6366F1',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#6366F1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false }, border: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#9CA3AF' }
                    },
                    y: {
                        grid: { color: '#F3F4F6' }, border: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#9CA3AF' }
                    }
                }
            }
        });
    })();

    // ─── Pass/Fail by Program (Bar Chart) ────────────────────
    (function () {
        const ctx = document.getElementById('passFailChart').getContext('2d');
        const programs = ['BSIS', 'BSIT', 'BSDRM', 'BSEd', 'BPA', 'BSTM'];
        // Simulated data based on actual counts
        const passed = [<?= round($passed_students * 0.3) ?>, <?= round($passed_students * 0.25) ?>, <?= round($passed_students * 0.2) ?>, <?= round($passed_students * 0.12) ?>, <?= round($passed_students * 0.08) ?>, <?= round($passed_students * 0.05) ?>];
        const failed = [<?= round($failed_students * 0.2) ?>, <?= round($failed_students * 0.3) ?>, <?= round($failed_students * 0.15) ?>, <?= round($failed_students * 0.15) ?>, <?= round($failed_students * 0.1) ?>, <?= round($failed_students * 0.1) ?>];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: programs,
                datasets: [
                    { label: 'Passed', data: passed, backgroundColor: '#10B981', borderRadius: 4, barPercentage: 0.6 },
                    { label: 'Failed', data: failed, backgroundColor: '#EF4444', borderRadius: 4, barPercentage: 0.6 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false }, border: { display: false },
                        ticks: { font: { family: 'Inter', size: 10 }, color: '#9CA3AF' }
                    },
                    y: {
                        grid: { color: '#F3F4F6' }, border: { display: false },
                        ticks: { font: { family: 'Inter', size: 10 }, color: '#9CA3AF' }, beginAtZero: true
                    }
                }
            }
        });
    })();
</script>
</body>

</html>