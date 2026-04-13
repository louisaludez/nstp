<?php
// includes/topbar.php

$portal_name = ($_SESSION['role'] === 'Admin') ? 'Coordinator Portal' : 'Instructor Portal';
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';
$user_name = $_SESSION['full_name'] ?? 'User';

// Calculate initials for the avatar
$name_parts = explode(' ', $user_name);
$initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));

// Fetch unread notifications
$notif_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE (user_id = ? OR role = ?) AND is_read = 0");
$notif_stmt->execute([$user_id, $role]);
$unread_count = $notif_stmt->fetchColumn();

$avatar_bg = ($role === 'Admin') ? '#4F46E5' : '#00695C';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom w-100">
    <div class="d-flex align-items-center">
        <!-- Desktop Sidebar Toggle Button -->
        <button id="sidebarToggleBtn" class="btn btn-sm btn-light border-0 me-3 d-none d-lg-block" style="background: transparent;" aria-label="Toggle Sidebar">
            <i class="bi bi-list fs-4 text-muted"></i>
        </button>
        <h5 class="fw-bold mb-0 text-dark"><?= $portal_name ?></h5>
    </div>
    
    <div class="d-flex align-items-center">
        <!-- Notification Dropdown -->
        <div class="dropdown me-3">
            <div class="position-relative" style="cursor: pointer;" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-5 text-muted"></i>
                <?php if ($unread_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;">
                    <?= $unread_count ?>
                </span>
                <?php endif; ?>
            </div>
            
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="width: 320px; border-radius: 12px; padding: 12px;">
                <li class="px-2 pb-2 border-bottom mb-2">
                    <h6 class="fw-bold mb-0">Notifications</h6>
                </li>
                <?php
                // Fetch recent 5 notifications
                $recent_notif = $pdo->prepare("SELECT * FROM notifications WHERE (user_id = ? OR role = ?) ORDER BY created_at DESC LIMIT 5");
                $recent_notif->execute([$user_id, $role]);
                $notifications = $recent_notif->fetchAll();
                
                if (count($notifications) > 0):
                    foreach ($notifications as $notif): 
                        $bg = $notif['is_read'] ? 'bg-transparent' : 'bg-light';
                        $font = $notif['is_read'] ? 'text-muted' : 'text-dark fw-medium';
                ?>
                    <li>
                        <a class="dropdown-item py-2 px-3 rounded-3 <?= $bg ?>" href="<?= htmlspecialchars($notif['link'] ?? '#') ?>" style="white-space: normal;">
                            <div class="<?= $font ?> small"><?= htmlspecialchars($notif['message']) ?></div>
                            <div class="text-muted" style="font-size: 0.70rem;"><?= htmlspecialchars($notif['created_at']) ?></div>
                        </a>
                    </li>
                <?php 
                    endforeach;
                else: 
                ?>
                    <li class="text-center py-3 text-muted small">No notifications</li>
                <?php endif; ?>
                
                <li class="pt-2 mt-2 border-top text-center">
                    <a href="javascript:void(0)" class="text-primary text-decoration-none small fw-medium" onclick="markAllRead()">Mark all as read</a>
                </li>
            </ul>
        </div>
        
        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <div class="d-flex align-items-center ms-2" style="cursor: pointer;" data-bs-toggle="dropdown">
                <span class="badge rounded-circle text-white d-flex justify-content-center align-items-center me-2" 
                      style="background-color: <?= $avatar_bg ?>; width: 35px; height: 35px; font-size: 0.9rem;">
                    <?= $initials ?>
                </span>
                <small class="fw-medium text-muted"><?= htmlspecialchars($role === 'Instructor' ? 'Prof. ' . $user_name : $user_name) ?></small>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: 8px;">
                <li><a class="dropdown-item text-dark fw-medium small mb-1" href="profile.php"><i class="bi bi-person-circle me-2 text-muted"></i>View Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger small fw-medium" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign out</a></li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const htmlEl = document.documentElement;
            htmlEl.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', htmlEl.classList.contains('sidebar-collapsed'));
        });
    }
});

function markAllRead() {
    fetch('../includes/mark_notifications_read.php', { method: 'POST' })
        .then(response => response.ok && window.location.reload());
}
</script>
