<?php
// includes/topbar.php

if ($_SESSION['role'] === 'Admin')
    $portal_name = 'Program Office';
elseif ($_SESSION['role'] === 'ROTC')
    $portal_name = 'ROTC Command';
else
    $portal_name = 'Instructor Portal';
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';
$user_name = $_SESSION['full_name'] ?? 'User';

// Calculate initials for the avatar
$name_parts = explode(' ', $user_name);
$initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));

// Fetch unread notifications
$unread_count = 0;
try {
    $notif_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE (user_id = ? OR role = ?) AND is_read = 0");
    $notif_stmt->execute([$user_id, $role]);
    $unread_count = $notif_stmt->fetchColumn();
} catch (Exception $e) {
}

// Context label matching reference design
$semester_label = 'NSTP Management System';
?>

<?php
// Determine title based on URL
$page_slug = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
$user_first_name = explode(' ', $user_name)[0];

if ($role === 'Admin') {
    $titles = [
        'dashboard' => "Welcome back, " . $user_first_name,
        'manage_sections' => "Sections & Students",
        'manage_instructors' => "Instructors & ROTC Officers",
        'submissions' => "Report & Activity Approvals",
        'calendar' => "Activity Calendar",
        'scan_grades' => "OCR Grade",
        'certificates' => "Certificates",
        'reports' => "Student Archive",
        'audit_logs' => "Audit Logs"
    ];
} elseif ($role === 'ROTC') {
    $titles = [
        'dashboard' => "Welcome back, " . $user_first_name,
        'platoons' => "Platoon Management",
        'platoon_management' => "Platoon Management",
        'cadets' => "Cadet Records",
        'rosters' => "Cadet Rosters",
        'master_calendar' => "Master Calendar",
        'activity_designs' => "Activity Designs",
        'accomplishment_reports' => "Accomplishment Reports"
    ];
} else {
    $titles = [
        'dashboard' => "Welcome back, " . $user_first_name,
        'my_section' => "My Classes",
        'activity_plans' => "Activity Plans",
        'reports' => "Accomplishment Reports",
        'announcements' => "Announcements"
    ];
}
$topbar_title = $titles[$page_slug] ?? ucfirst(str_replace('_', ' ', $page_slug));
?>
<div class="topbar-fixed bg-white d-flex justify-content-between align-items-center">
    <!-- Left: Toggle + Context + Page title (matching reference) -->
    <div class="d-flex align-items-center gap-3">
        <button id="sidebarToggleBtn"
            class="btn btn-sm border-0 p-2 d-none d-lg-flex align-items-center justify-content-center"
            style="border-radius:8px;color:#64748B;transition:all 0.15s;"
            onmouseover="this.style.background='#F1F5F9';this.style.color='#334155'"
            onmouseout="this.style.background='transparent';this.style.color='#64748B'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" style="width: 20px; height: 20px;">
                <line x1="4" x2="20" y1="12" y2="12" />
                <line x1="4" x2="20" y1="6" y2="6" />
                <line x1="4" x2="20" y1="18" y2="18" />
            </svg>
        </button>
        <div>
            <div class="semester-label" style="font-size:0.8rem; color:#64748B; margin-bottom:2px; font-weight:500;">
                <?= htmlspecialchars($semester_label) ?>
            </div>
            <h2 style="font-size:1.45rem; font-weight:700; color:#0F172A; margin:0; letter-spacing:-0.02em;">
                <?= htmlspecialchars($topbar_title) ?>
            </h2>
        </div>
    </div>

    <!-- Right: Search + Actions -->
    <div class="d-flex align-items-center gap-3">
        <!-- Search -->
        <div class="position-relative d-none d-md-block">
            <span class="position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:#9CA3AF;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" style="width: 16px; height: 16px;">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
            </span>
            <input type="text" class="search-input-figma" placeholder="Search..." style="padding-left:36px;">
        </div>


        <!-- Notifications -->
        <div class="dropdown">
            <div class="position-relative" style="cursor:pointer;" data-bs-toggle="dropdown" aria-expanded="false">
                <span style="color:#9CA3AF;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" style="width: 18px; height: 18px;">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                    </svg>
                </span>
                <?php if ($unread_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size:0.55rem;">
                        <?= $unread_count ?>
                    </span>
                <?php endif; ?>
            </div>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                style="width:320px;border-radius:12px;padding:12px;">
                <li class="px-2 pb-2 border-bottom mb-2">
                    <h6 class="fw-bold mb-0" style="font-size:0.9rem;">Notifications</h6>
                </li>
                <?php
                try {
                    $recent_notif = $pdo->prepare("SELECT * FROM notifications WHERE (user_id = ? OR role = ?) ORDER BY created_at DESC LIMIT 5");
                    $recent_notif->execute([$user_id, $role]);
                    $notifications = $recent_notif->fetchAll();
                } catch (Exception $e) {
                    $notifications = [];
                }

                if (count($notifications) > 0):
                    foreach ($notifications as $notif):
                        $bg = $notif['is_read'] ? 'bg-transparent' : 'bg-light';
                        $font = $notif['is_read'] ? 'text-muted' : 'text-dark fw-medium';
                        ?>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded-3 <?= $bg ?>"
                                href="<?= htmlspecialchars($notif['link'] ?? '#') ?>" style="white-space:normal;">
                                <div class="<?= $font ?> small"><?= htmlspecialchars($notif['message']) ?></div>
                                <div class="text-muted" style="font-size:0.70rem;"><?= htmlspecialchars($notif['created_at']) ?>
                                </div>
                            </a>
                        </li>
                        <?php
                    endforeach;
                else:
                    ?>
                    <li class="text-center py-3 text-muted small">No notifications</li>
                <?php endif; ?>

                <li class="pt-2 mt-2 border-top text-center">
                    <a href="javascript:void(0)" class="text-decoration-none small fw-medium" style="color:#6366F1;"
                        onclick="markAllRead()">Mark all as read</a>
                </li>
            </ul>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        if (toggleBtn) {
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