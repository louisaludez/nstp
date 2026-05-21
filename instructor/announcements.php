<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    header("Location: ../login");
    exit;
}

require 'controllers/AnnouncementsController.php';

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
                <input type="text" id="announcementSearch" class="form-control" placeholder="Search announcements..." style="padding-left: 36px; border-radius: 8px; font-size: 0.85rem; border: 1px solid #E5E7EB; width: 280px; box-shadow: none;">
            </div>
        </div>
    </div>

    <!-- Announcements Card -->
    <div style="background: white; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        
        <div class="list-group list-group-flush" id="announcementsList" style="border-radius: 12px; overflow: hidden;">
            
            <?php if (count($announcements) > 0): ?>
                <?php foreach ($announcements as $idx => $ann): 
                    $avatarColor = getSourceAvatarColor($ann['source']);
                    $initials = getSourceInitials($ann['source']);
                    $timeLabel = timeAgo($ann['created_at']);
                    $isLast = ($idx === count($announcements) - 1);
                    $borderStyle = $isLast ? '' : 'border-bottom: 1px solid #F3F4F6 !important;';
                ?>
                <div class="list-group-item border-0 announcement-item" style="padding: 24px; <?= $borderStyle ?>">
                    <div class="d-flex align-items-start">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: <?= $avatarColor ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.95rem; flex-shrink: 0; margin-right: 24px;">
                            <?= $initials ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <span style="font-weight: 500; color: #6B7280;"><?= htmlspecialchars($ann['source']) ?></span> · <?= htmlspecialchars($timeLabel) ?>
                                </div>
                                <?php if ($ann['is_pinned']): ?>
                                <div style="color: #10B981; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em; display: flex; align-items: center; gap: 4px;">
                                    <i class="bi bi-pin-angle-fill"></i> PINNED
                                </div>
                                <?php endif; ?>
                            </div>
                            <h6 style="color: #111827; font-weight: 600; margin-bottom: 4px; font-size: 0.95rem;"><?= htmlspecialchars($ann['title']) ?></h6>
                            <p style="color: #4B5563; font-size: 0.85rem; margin-bottom: 0;"><?= htmlspecialchars($ann['content']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item border-0 text-center py-5">
                    <div style="color: #9CA3AF; margin-bottom: 8px;"><i class="bi bi-megaphone" style="font-size: 1.5rem;"></i></div>
                    <div class="text-muted" style="font-size: 0.85rem;">No announcements at this time.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Client-side search filtering for announcements
document.getElementById('announcementSearch').addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    const items = document.querySelectorAll('.announcement-item');
    
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(query) ? '' : 'none';
    });
});
</script>
</body>
</html>
