<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login");
    exit;
}

require 'controllers/ReportsController.php';

$extra_css = ['../assets/css/pages/admin/reports.css'];
$extra_js = ['https://cdn.jsdelivr.net/npm/chart.js', '../assets/js/pages/admin/reports.js'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-5 w-100" style="background-color: #F4F6F9;">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">Reports &amp; Analytics</h3>
            <p class="text-muted mb-0">Comprehensive NSTP program statistics</p>
        </div>
        <a href="export_reports.php" class="btn btn-brand border-0">
            <i class="bi bi-download me-2"></i> Export Report
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="panel-container d-flex justify-content-between align-items-center p-4">
                <div>
                    <small class="text-muted fw-medium d-block mb-1">Overall Pass Rate</small>
                    <h2 class="fw-bold mb-0" style="color: #111827;"><?= $pass_rate ?>%</h2>
                </div>
                <div class="icon-circle bg-green-brand"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel-container d-flex justify-content-between align-items-center p-4">
                <div>
                    <small class="text-muted fw-medium d-block mb-1">Total Enrolled</small>
                    <h2 class="fw-bold mb-0" style="color: #111827;"><?= number_format($total_students) ?></h2>
                </div>
                <div class="icon-circle bg-blue-brand"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel-container d-flex justify-content-between align-items-center p-4">
                <div>
                    <small class="text-muted fw-medium d-block mb-1">Certificates Issued</small>
                    <h2 class="fw-bold mb-0" style="color: #111827;"><?= number_format($total_certs) ?></h2>
                </div>
                <div class="icon-circle bg-purple-brand"><i class="bi bi-award-fill"></i></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="panel-container">
                <h6 class="fw-bold mb-4" style="color: #111827;">Student Distribution by Component</h6>
                <canvas id="barChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="panel-container">
                <h6 class="fw-bold mb-4" style="color: #111827;">Overall Pass/Fail Distribution</h6>
                <div style="width: 80%; margin: auto;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-container">
        <h6 class="fw-bold mb-4" style="color: #111827;">Instructor Workload Report</h6>
        <div class="table-responsive">
            <table class="table table-borderless table-workload align-middle mb-0">
                <thead>
                    <tr>
                        <th>Instructor</th><th>Students</th><th>Sections</th><th>Avg per Section</th><th>Workload</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($instructor_workload) > 0): ?>
                        <?php foreach ($instructor_workload as $inst): ?>
                            <?php 
                                $student_count = $inst['student_count'] ?? 0; 
                                $avg_per_section = $inst['section_count'] > 0 ? round($student_count / $inst['section_count']) : 0;
                                if ($inst['section_count'] >= 3) { $badge = '<span class="badge-high">High</span>'; }
                                elseif ($inst['section_count'] == 2) { $badge = '<span class="badge-medium">Medium</span>'; }
                                else { $badge = '<span class="badge-low">Low</span>'; }
                            ?>
                            <tr>
                                <td class="fw-medium text-dark">Prof. <?= htmlspecialchars($inst['instructor']) ?></td>
                                <td><?= $student_count ?></td>
                                <td><?= htmlspecialchars($inst['section_count']) ?></td>
                                <td><?= $avg_per_section ?></td>
                                <td><?= $badge ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4">No active instructors found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<script>
// Pass PHP data to JS
window.chartData = {
    cwts_p: <?= $cwts_p ?>, lts_p: <?= $lts_p ?>, rotc_p: <?= $rotc_p ?>,
    cwts_f: <?= $cwts_f ?>, lts_f: <?= $lts_f ?>, rotc_f: <?= $rotc_f ?>,
    cwts_total: <?= $cwts_total ?>, lts_total: <?= $lts_total ?>, rotc_total: <?= $rotc_total ?>,
    total_passed: <?= $total_passed ?>, total_failed: <?= $total_failed ?>,
    pass_rate: <?= $pass_rate ?>, fail_rate: <?= $total_graded > 0 ? round(($total_failed/$total_graded)*100,1) : 0 ?>
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/pages/admin/reports.js"></script>
</body>
</html>
