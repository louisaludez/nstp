<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

$instructor_name = $_SESSION['full_name'];

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/instructor_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100" style="background-color: #F9FAFB; min-height: 100vh;">
    
    <?php include '../includes/topbar.php'; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h5 class="fw-bold mb-1" style="color: #111827; font-size: 1.15rem;">Announcements</h5>
            <div class="text-muted" style="font-size: 0.85rem;">Official updates from the NSTP & Dean's offices</div>
        </div>
        <div>
            <div class="position-relative">
                <i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                <input type="text" class="form-control" placeholder="Search announcements..." style="padding-left: 36px; border-radius: 8px; font-size: 0.85rem; border: 1px solid #E5E7EB; width: 280px; box-shadow: none;">
            </div>
        </div>
    </div>

    <!-- Announcements Card -->
    <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        
        <div class="list-group list-group-flush" style="border-radius: 12px; overflow: hidden;">
            
            <!-- Item 1 (Pinned) -->
            <div class="list-group-item border-0" style="padding: 24px; border-bottom: 1px solid #F3F4F6 !important;">
                <div class="d-flex align-items-start">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #3B82F6; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.95rem; flex-shrink: 0; margin-right: 24px;">
                        NO
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <span style="font-weight: 500; color: #6B7280;">NSTP Office</span> · 2h ago
                            </div>
                            <div style="color: #10B981; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em; display: flex; align-items: center; gap: 4px;">
                                <i class="bi bi-pin-angle-fill"></i> PINNED
                            </div>
                        </div>
                        <h6 style="color: #111827; font-weight: 600; margin-bottom: 4px; font-size: 0.95rem;">Mid-semester accomplishment reports due May 22</h6>
                        <p style="color: #4B5563; font-size: 0.85rem; margin-bottom: 0;">All instructors must submit consolidated accomplishment reports by 11:59 PM.</p>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="list-group-item border-0" style="padding: 24px; border-bottom: 1px solid #F3F4F6 !important;">
                <div class="d-flex align-items-start">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #10B981; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.95rem; flex-shrink: 0; margin-right: 24px;">
                        DO
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <span style="font-weight: 500; color: #6B7280;">Dean's Office</span> · Yesterday
                            </div>
                        </div>
                        <h6 style="color: #111827; font-weight: 600; margin-bottom: 4px; font-size: 0.95rem;">Field activity safety briefing — mandatory</h6>
                        <p style="color: #4B5563; font-size: 0.85rem; margin-bottom: 0;">Briefing scheduled Friday, May 16, 3:00 PM at the AVR.</p>
                    </div>
                </div>
            </div>

            <!-- Item 3 -->
            <div class="list-group-item border-0" style="padding: 24px; border-bottom: 1px solid #F3F4F6 !important;">
                <div class="d-flex align-items-start">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #A855F7; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.95rem; flex-shrink: 0; margin-right: 24px;">
                        PC
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <span style="font-weight: 500; color: #6B7280;">Program Coordinator</span> · May 9
                            </div>
                        </div>
                        <h6 style="color: #111827; font-weight: 600; margin-bottom: 4px; font-size: 0.95rem;">Updated rubric for accomplishment reports</h6>
                        <p style="color: #4B5563; font-size: 0.85rem; margin-bottom: 0;">Rubric v3.2 is now in effect.</p>
                    </div>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="list-group-item border-0" style="padding: 24px;">
                <div class="d-flex align-items-start">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #F97316; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.95rem; flex-shrink: 0; margin-right: 24px;">
                        RG
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <span style="font-weight: 500; color: #6B7280;">Registrar</span> · May 8
                            </div>
                        </div>
                        <h6 style="color: #111827; font-weight: 600; margin-bottom: 4px; font-size: 0.95rem;">Grade encoding window opens May 25</h6>
                        <p style="color: #4B5563; font-size: 0.85rem; margin-bottom: 0;">OCR-assisted grade upload available end of month.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
