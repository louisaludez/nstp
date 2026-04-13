<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch Real Data: Instructor Workload
$stmtWorkload = $pdo->query("
    SELECT 
        u.full_name as instructor, 
        COUNT(DISTINCT s.id) as section_count,
        (SELECT COUNT(e.student_id) FROM enrollments e JOIN sections sec ON e.section_id = sec.id WHERE sec.instructor_id = u.id) as student_count
    FROM users u
    LEFT JOIN sections s ON u.id = s.instructor_id
    WHERE u.role = 'Instructor'
    GROUP BY u.id
    ORDER BY section_count DESC
");
$instructor_workload = $stmtWorkload->fetchAll();

// Fetch Real Data: Total Enrolled Students
$stmtTotalStudents = $pdo->query("SELECT COUNT(*) FROM students");
$total_students = $stmtTotalStudents->fetchColumn();

// Fetch Pass/Fail counts
$stmtStats = $pdo->query("
    SELECT 
        SUM(CASE WHEN status = 'Passed' THEN 1 ELSE 0 END) as passed_count,
        SUM(CASE WHEN status = 'Failed' THEN 1 ELSE 0 END) as failed_count
    FROM enrollments
");
$global_stats = $stmtStats->fetch();
$total_passed = $global_stats['passed_count'] ?? 0;
$total_failed = $global_stats['failed_count'] ?? 0;
$total_graded = $total_passed + $total_failed;
$pass_rate = $total_graded > 0 ? round(($total_passed / $total_graded) * 100, 1) : 0;

// Fetch Certs Issued
$stmtCerts = $pdo->query("SELECT COUNT(serial_number) FROM enrollments WHERE serial_number IS NOT NULL");
$total_certs = $stmtCerts->fetchColumn();

// Fetch Component Breakdown
$stmtComp = $pdo->query("
    SELECT 
        sec.component,
        SUM(CASE WHEN e.status = 'Passed' THEN 1 ELSE 0 END) as passed_count,
        SUM(CASE WHEN e.status = 'Failed' THEN 1 ELSE 0 END) as failed_count
    FROM enrollments e
    JOIN sections sec ON e.section_id = sec.id
    GROUP BY sec.component
");
$comp_data = [];
while ($row = $stmtComp->fetch(PDO::FETCH_ASSOC)) {
    if ($row['component']) {
        $comp_data[$row['component']] = $row;
    }
}

$cwts_p = $comp_data['CWTS']['passed_count'] ?? 0;
$cwts_f = $comp_data['CWTS']['failed_count'] ?? 0;
$lts_p = $comp_data['LTS']['passed_count'] ?? 0;
$lts_f = $comp_data['LTS']['failed_count'] ?? 0;
$rotc_p = $comp_data['ROTC']['passed_count'] ?? 0;
$rotc_f = $comp_data['ROTC']['failed_count'] ?? 0;

include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .panel-container {
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 24px;
        height: 100%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .btn-brand {
        background-color: var(--primary-active, #4A46D6);
        color: white;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 16px;
    }
    .btn-brand:hover { background-color: var(--primary-bg, #2B2866); color: white; }
    
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }
    
    .bg-green-brand { background-color: #10B981; }
    .bg-blue-brand { background-color: #3B82F6; }
    .bg-purple-brand { background-color: #8B5CF6; }
    
    /* Table Styling */
    .table-workload th {
        font-size: 0.85rem;
        color: #111827;
        font-weight: 600;
        background-color: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
        padding: 12px 16px;
    }
    .table-workload td {
        padding: 16px;
        color: #4B5563;
        border-bottom: 1px solid #F3F4F6;
    }
    
    .badge-high { background-color: #FEE2E2; color: #DC2626; padding: 4px 10px; border-radius: 12px; font-weight: 500; font-size: 0.75rem;}
    .badge-medium { background-color: #FEF3C7; color: #D97706; padding: 4px 10px; border-radius: 12px; font-weight: 500; font-size: 0.75rem;}
    .badge-low { background-color: #D1FAE5; color: #059669; padding: 4px 10px; border-radius: 12px; font-weight: 500; font-size: 0.75rem;}
</style>

<div class="flex-grow-1 p-5 w-100" style="background-color: #F4F6F9;">
    
    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">Reports & Analytics</h3>
            <p class="text-muted mb-0">Comprehensive NSTP program statistics</p>
        </div>
        <button type="button" class="btn btn-brand border-0">
            <i class="bi bi-download me-2"></i> Export Report
        </button>
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
                        <th>Instructor</th>
                        <th>Students</th>
                        <th>Sections</th>
                        <th>Avg per Section</th>
                        <th>Workload</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($instructor_workload) > 0): ?>
                        <?php foreach ($instructor_workload as $inst): ?>
                            <?php 
                                $student_count = $inst['student_count'] ?? 0; 
                                $avg_per_section = $inst['section_count'] > 0 ? round($student_count / $inst['section_count']) : 0;
                                
                                // Determine Workload Badge
                                if ($inst['section_count'] >= 3) {
                                    $badge = '<span class="badge-high">High</span>';
                                } elseif ($inst['section_count'] == 2) {
                                    $badge = '<span class="badge-medium">Medium</span>';
                                } else {
                                    $badge = '<span class="badge-low">Low</span>';
                                }
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
    // Exact colors from the Figma design
    const colorPassed = '#10B981'; // Green
    const colorFailed = '#EF4444'; // Red

    // 1. Setup Bar Chart (Student Distribution)
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['CWTS', 'LTS', 'ROTC'],
            datasets: [
                {
                    label: 'Passed',
                    data: [<?= $cwts_p ?>, <?= $lts_p ?>, <?= $rotc_p ?>],
                    backgroundColor: colorPassed,
                    borderRadius: 4
                },
                {
                    label: 'Failed',
                    data: [<?= $cwts_f ?>, <?= $lts_f ?>, <?= $rotc_f ?>],
                    backgroundColor: colorFailed,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [4, 4] } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Setup Pie/Doughnut Chart (Pass/Fail)
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Passed: <?= $total_passed ?> (<?= $pass_rate ?>%)', 'Failed: <?= $total_failed ?> (<?= $total_graded > 0 ? round(($total_failed/$total_graded)*100,1) : 0 ?>%)'],
            datasets: [{
                data: [<?= $total_passed ?>, <?= $total_failed ?>],
                backgroundColor: [colorPassed, colorFailed],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>