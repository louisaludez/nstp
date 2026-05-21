<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

require 'controllers/DashboardController.php';

// Prepare variables for the new layout
$first_name = explode(' ', $instructor_name)[0];

// Total students across all sections
$stmtAllStu = $pdo->prepare("SELECT COUNT(e.student_id) FROM enrollments e JOIN sections s ON e.section_id = s.id WHERE s.instructor_id = ?");
$stmtAllStu->execute([$instructor_id]);
$total_students_all = $stmtAllStu->fetchColumn() ?: 0;

// Reports Pending
$stmtPend = $pdo->prepare("SELECT COUNT(*) FROM activity_plans WHERE instructor_id = ? AND status = 'Pending'");
$stmtPend->execute([$instructor_id]);
$pending_reports = $stmtPend->fetchColumn() ?: 0;

// Approved YTD
$stmtApp = $pdo->prepare("SELECT COUNT(*) FROM activity_plans WHERE instructor_id = ? AND status = 'Approved'");
$stmtApp->execute([$instructor_id]);
$approved_reports = $stmtApp->fetchColumn() ?: 0;

// Unique components
$handled_components = [];
foreach ($all_assigned_sections as $sec) {
    if (!in_array($sec['component'], $handled_components)) {
        $handled_components[] = $sec['component'];
    }
}
$handled_components_str = implode(' · ', $handled_components);
if (empty($handled_components_str)) $handled_components_str = 'None';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background-color: #F9FAFB; min-height: 100vh;">

    <?php include '../includes/topbar.php'; ?>
    


    <!-- Stat Cards -->
    <div class="row g-3 mb-5">
        <!-- Assigned Sections -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card-figma h-100 d-flex flex-column justify-content-center px-4 py-3" style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #6366F1; color: white; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.75rem;">Assigned Sections</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.2;"><?= count($all_assigned_sections) ?></div>
                    </div>
                </div>
                <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($handled_components_str) ?></div>
            </div>
        </div>
        
        <!-- Total Students -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card-figma h-100 d-flex flex-column justify-content-center px-4 py-3" style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #10B981; color: white; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.75rem;">Total Students</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.2;"><?= number_format($total_students_all) ?></div>
                    </div>
                </div>
                <div class="text-muted" style="font-size: 0.75rem;">Across all sections</div>
            </div>
        </div>
        
        <!-- Reports Pending -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card-figma h-100 d-flex flex-column justify-content-center px-4 py-3" style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #F97316; color: white; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="12" y1="11" x2="16" y2="11"/><line x1="12" y1="16" x2="16" y2="16"/></svg>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.75rem;">Reports Pending</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.2;"><?= $pending_reports ?></div>
                    </div>
                </div>
                <div class="text-muted" style="font-size: 0.75rem;"><?= $due_this_week ?> due this week</div>
            </div>
        </div>
        
        <!-- Approved YTD -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card-figma h-100 d-flex flex-column justify-content-center px-4 py-3" style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #A855F7; color: white; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><polyline points="9 15 11 17 15 13"/></svg>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.75rem;">Approved YTD</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.2;"><?= $approved_reports ?></div>
                    </div>
                </div>
                <div class="text-muted" style="font-size: 0.75rem;">+<?= $approved_this_month ?> this month</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content: Your Sections -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-end mb-3">
                <div>
                    <h5 class="fw-bold mb-1" style="color: #111827;">Your Sections</h5>
                    <div class="text-muted" style="font-size: 0.85rem;">CWTS & LTS classes you handle this semester</div>
                </div>
                <a href="my_section" class="text-decoration-none" style="color: #10B981; font-size: 0.85rem; font-weight: 600;">View all <i class="bi bi-arrow-up-right"></i></a>
            </div>

            <div class="row g-3">
                <?php if (count($all_assigned_sections) > 0): ?>
                    <?php 
                    $colors = ['#3B82F6', '#A855F7', '#F97316', '#10B981']; // Blue, Purple, Orange, Green
                    $idx = 0;
                    foreach ($all_assigned_sections as $sec): 
                        $color = $colors[$idx % count($colors)];
                        
                        // Count students for this section
                        $stmtStuCount = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE section_id = ?");
                        $stmtStuCount->execute([$sec['id']]);
                        $sec_student_count = $stmtStuCount->fetchColumn();
                        
                        $full_name = ($sec['component'] == 'CWTS') ? 'Community Welfare Training Service' : (($sec['component'] == 'LTS') ? 'Literacy Training Service' : 'Reserve Officers\' Training Corps');
                        
                        // Use a stable hash per section to generate consistent room/day/time
                        $hash = crc32($sec['id'] . $sec['section_name']);
                        $room = 'Bldg. ' . chr(65 + ($hash % 4)) . ' - Rm ' . (100 + ($hash % 200));
                        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
                        $day = $days[$hash % 5];
                        $startHr = 7 + ($hash % 5);  // 7-11
                        $endHr = $startHr + 2;
                        $time = $startHr . ':00-' . $endHr . ':00 ' . ($endHr >= 12 ? 'PM' : 'AM');
                        
                        // Determine semester badge from the section's actual semester field
                        $sem = $sec['semester'] ?? '1st';
                        if ($sem === '2nd') {
                            $semester_text = "2nd Semester";
                            $semester_bg = "#EFF6FF";
                            $semester_color = "#3B82F6";
                        } elseif ($sem === 'Summer') {
                            $semester_text = "Summer";
                            $semester_bg = "#FFF7ED";
                            $semester_color = "#F97316";
                        } else {
                            $semester_text = "1st Semester";
                            $semester_bg = "#ECFDF5";
                            $semester_color = "#10B981";
                        }
                    ?>
                    <div class="col-md-6">
                        <div style="background: white; border: 1px solid #E5E7EB; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column;">
                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 48px; height: 48px; border-radius: 50%; background: <?= $color ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                                        <?= htmlspecialchars($sec['component']) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #111827; font-size: 1rem;"><?= htmlspecialchars($sec['component'] . ' 1 · ' . $sec['section_name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.8rem;"><?= $full_name ?></div>
                                    </div>
                                </div>
                                <span style="background: <?= $semester_bg ?>; color: <?= $semester_color ?>; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500;">
                                    <?= $semester_text ?>
                                </span>
                            </div>
                            
                            <!-- Info Row -->
                            <div class="d-flex justify-content-between align-items-center mt-auto" style="font-size: 0.8rem; color: #6B7280;">
                                <div class="d-flex align-items-center gap-2"><i class="bi bi-people" style="font-size: 1.1rem; color: #9CA3AF;"></i> <?= $sec_student_count ?> students</div>
                                <div class="d-flex align-items-center gap-2"><i class="bi bi-geo-alt" style="font-size: 1.1rem; color: #9CA3AF;"></i> <?= $room ?></div>
                                <div class="d-flex align-items-center gap-2"><i class="bi bi-clock" style="font-size: 1.1rem; color: #9CA3AF;"></i> <span style="white-space: nowrap;"><?= $day . ' · ' . $time ?></span></div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        $idx++;
                    endforeach; 
                    ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center" style="background: white; border: 1px dashed #E5E7EB; border-radius: 16px; height: 100%; min-height: 250px;">
                            <div style="width: 56px; height: 56px; border-radius: 50%; background: #F3F4F6; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #9CA3AF; margin-bottom: 16px;">
                                <i class="bi bi-journal-x"></i>
                            </div>
                            <h6 style="color: #374151; font-weight: 600; margin-bottom: 4px;">No Assigned Sections</h6>
                            <p class="text-muted small mb-0" style="max-width: 250px;">You haven't been assigned any sections for this semester yet.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Calendar of Activities -->
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; padding: 24px;">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h6 class="fw-bold mb-1" style="color: #111827; font-size: 1rem;">Calendar of Activities</h6>
                        <div class="text-muted" style="font-size: 0.8rem;">Two-week preview of official dates</div>
                    </div>
                    <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.8rem;">
                        <i class="bi bi-calendar"></i> <?= $calendar_month_label ?>
                    </div>
                </div>
                
                <div class="d-flex flex-wrap" style="gap: 8px;">
                    <?php /* $calendar_days is built dynamically in DashboardController */ ?>
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; width: 100%;">
                        <?php foreach ($calendar_days as $cday): ?>
                            <div class="d-flex flex-column align-items-center justify-content-center py-2" style="border: 1px solid <?= !empty($cday['active']) ? '#E5E7EB' : '#F9FAFB' ?>; border-radius: 10px; background: <?= !empty($cday['active']) ? '#F9FAFB' : 'white' ?>; min-height: 70px;">
                                <div style="font-size: 0.65rem; color: #9CA3AF; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;"><?= $cday['day'] ?></div>
                                <div style="font-size: 0.95rem; font-weight: 700; color: #111827; margin-bottom: 6px;"><?= $cday['date'] ?></div>
                                <div style="width: 4px; height: 4px; border-radius: 50%; background-color: <?= $cday['dot'] ?: 'transparent' ?>;"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Submission Tracker -->
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 16px; display: flex; flex-direction: column; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <!-- Header -->
                <div style="padding: 24px 24px 16px;">
                    <h6 class="fw-bold mb-1" style="color: #111827; font-size: 1rem;">Submission Tracker</h6>
                    <div class="text-muted" style="font-size: 0.8rem;">Upcoming activity reports</div>
                </div>
                
                <!-- List -->
                <div class="list-group list-group-flush" style="padding: 0 12px;">
                    <?php if (count($tracker_items) > 0): ?>
                        <?php foreach ($tracker_items as $ti_idx => $ti): 
                            $is_last = $ti_idx === count($tracker_items) - 1;
                            $border_style = $is_last ? '' : 'border-bottom: 1px solid #F3F4F6 !important;';
                        ?>
                        <div class="list-group-item border-0 py-3 d-flex justify-content-between align-items-center" style="<?= $border_style ?>">
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 500; color: #374151; margin-bottom: 2px;"><?= $ti['title'] ?></div>
                                <div style="font-size: 0.75rem; color: #6B7280;"><?= $ti['subtitle'] ?></div>
                            </div>
                            <span style="font-size: 0.7rem; padding: 4px 12px; border-radius: 20px; background: transparent; border: 1px solid <?= $ti['badge_border'] ?>; color: <?= $ti['badge_color'] ?>; font-weight: 500; display: flex; align-items: center; gap: 4px;">
                                <i class="bi <?= $ti['badge_icon'] ?>" style="font-size: 0.65rem;"></i> <?= $ti['badge_label'] ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item border-0 py-4 text-center">
                            <div class="text-muted" style="font-size: 0.85rem;">No submissions yet</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Footer -->
                <div class="mt-auto" style="padding: 20px 24px; border-top: 1px solid #F3F4F6; display: flex; justify-content: space-between; align-items: center;">
                    <div class="text-muted" style="font-size: 0.8rem;"><?= $due_this_week ?> due this week</div>
                    <a href="activity_plans" class="btn-figma primary" style="padding: 8px 16px; font-size: 0.85rem; font-weight: 500; border-radius: 6px; box-shadow: 0 1px 2px rgba(16,185,129,0.2); background: #059669; color: white; border: none; text-decoration: none;">Submit Report</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
