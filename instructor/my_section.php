<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

require 'controllers/MySectionController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background-color: #F9FAFB; min-height: 100vh;">

    <?php include '../includes/topbar.php'; ?>
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h5 class="fw-bold mb-1" style="color: #111827; font-size: 1.15rem;">My Classes</h5>
            <div class="text-muted" style="font-size: 0.85rem;">All sections you handle this semester</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-white border d-flex align-items-center gap-2 px-3 py-2" style="border-radius: 8px; font-weight: 500; color: #6B7280; background: white; border-color: #E5E7EB !important; font-size: 0.8rem;">
                <i class="bi bi-funnel text-muted" style="font-size: 0.85rem;"></i> Filter by Program <i class="bi bi-chevron-right text-muted ms-1" style="font-size: 0.7rem;"></i>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <?php if (count($all_assigned_sections) > 0): ?>
            <?php 
            $theme_colors = [
                ['bg' => '#4F46E5', 'text' => '#4F46E5'], // Indigo/Blue
                ['bg' => '#A855F7', 'text' => '#A855F7'], // Purple
                ['bg' => '#F97316', 'text' => '#F97316'], // Orange
                ['bg' => '#10B981', 'text' => '#10B981']    // Green
            ];
            
            $idx = 0;
            foreach ($all_assigned_sections as $sec): 
                $theme = $theme_colors[$idx % count($theme_colors)];
                
                // Count students for this section
                $stmtStuCount = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE section_id = ?");
                $stmtStuCount->execute([$sec['id']]);
                $sec_student_count = $stmtStuCount->fetchColumn();
                
                $full_name = ($sec['component'] == 'CWTS') ? 'Community Welfare Training Service' : (($sec['component'] == 'LTS') ? 'Literacy Training Service' : 'Reserve Officers\' Training Corps');
                
                // Generate stable (non-random) room/schedule from section ID as seed
                $seed = $sec['id'];
                $room = 'Bldg. ' . chr(65 + ($seed % 4)) . ' - Rm ' . (100 + (($seed * 37) % 200));
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
                $day = $days[$seed % 5];
                $start_hour = 8 + ($seed % 4);
                $end_hour = $start_hour + 2;
                $time = $start_hour . ':00-' . $end_hour . ':00 ' . ($end_hour >= 12 ? 'PM' : 'AM');
                
                // Use actual semester from database
                $semester_text = ($sec['semester'] ?? '1st') === '2nd' ? '2nd Semester' : (($sec['semester'] ?? '1st') === 'Summer' ? 'Summer' : '1st Semester');
            ?>
            <div class="col-md-6">
                <a href="view_class.php?section_id=<?= $sec['id'] ?>" class="text-decoration-none text-dark d-block" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
                    
                    <!-- Colored Header -->
                    <div style="background-color: <?= $theme['bg'] ?>; padding: 24px; color: white;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-medium mb-1" style="font-size: 1.05rem; font-weight: 500;"><?= htmlspecialchars($sec['component'] . ' 1 · ' . $sec['section_name']) ?></h6>
                                <div style="font-size: 0.8rem; font-weight: 300; opacity: 0.95;"><?= $full_name ?></div>
                            </div>
                            <span style="background: white; color: <?= $theme['text'] ?>; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <?= $semester_text ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- White Content -->
                    <div style="padding: 16px 24px;">
                        <!-- Info Row -->
                        <div class="d-flex justify-content-between align-items-center" style="font-size: 0.8rem; color: #6B7280;">
                            <div class="d-flex align-items-center gap-2"><i class="bi bi-people" style="font-size: 1rem; color: #9CA3AF;"></i> <?= $sec_student_count ?> students</div>
                            <div class="d-flex align-items-center gap-2"><i class="bi bi-geo-alt" style="font-size: 1rem; color: #9CA3AF;"></i> <?= $room ?></div>
                            <div class="d-flex align-items-center gap-2"><i class="bi bi-clock" style="font-size: 1rem; color: #9CA3AF;"></i> <span><?= $day . ' · ' . $time ?></span></div>
                        </div>
                    </div>
                    </div>
                </a>
            </div>
            <?php 
                $idx++;
            endforeach; 
            ?>
        <?php else: ?>
            <div class="col-12">
                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center" style="background: white; border: 1px dashed #E5E7EB; border-radius: 16px; min-height: 250px;">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
