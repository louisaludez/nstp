<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require 'controllers/AuditLogsController.php';

$extra_css = ['../assets/css/style.css'];
include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<div class="flex-grow-1 p-4 p-lg-5 w-100">

    <?php include '../includes/topbar.php'; ?>

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 mt-2">
        <div>
            <h2 class="mb-1" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Audit Logs Overview</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Immutable record of every action taken in the program office</p>
        </div>
        
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Search -->
            <div class="d-flex align-items-center bg-white border rounded-3 px-3" style="min-width: 240px; height: 38px; border-color: #E5E7EB !important;">
                <i class="bi bi-search text-muted" style="font-size: 0.8rem;"></i>
                <input type="text" class="form-control border-0 shadow-none bg-transparent py-0 h-100 ms-2" id="logSearch" placeholder="Search logs..." style="font-size: 0.85rem;">
            </div>
            
            <!-- Dropdown -->
            <div class="dropdown">
                <button class="btn btn-white border bg-white d-flex justify-content-between align-items-center rounded-3" type="button" data-bs-toggle="dropdown" style="width: 150px; height: 38px; font-size: 0.85rem; border-color: #E5E7EB !important; color: #374151;">
                    <span id="selectedFilter">All Logs</span>
                    <i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;"></i>
                </button>
                <ul class="dropdown-menu shadow-sm border-0 mt-1 py-2" style="font-size: 0.85rem; border-radius: 10px; min-width: 150px;">
                    <li><h6 class="dropdown-header text-muted text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Filter by Type</h6></li>
                    <li><a class="dropdown-item d-flex justify-content-between align-items-center text-primary py-2" href="#" style="background-color: #F9FAFB;">All Logs <i class="bi bi-check2"></i></a></li>
                    <li><a class="dropdown-item py-2" href="#">Approvals</a></li>
                    <li><a class="dropdown-item py-2" href="#">System Logs</a></li>
                    <li><a class="dropdown-item py-2" href="#">Submissions</a></li>
                    <li><a class="dropdown-item py-2" href="#">Alerts</a></li>
                    <li><a class="dropdown-item py-2" href="#">Edits</a></li>
                </ul>
            </div>
            
            <!-- Export -->
            <button type="button" class="btn btn-white border bg-white d-flex align-items-center gap-2 rounded-3" style="height: 38px; font-size: 0.85rem; border-color: #E5E7EB !important; color: #374151;">
                <i class="bi bi-download text-muted"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- List Section -->
    <div class="bg-white rounded-4 shadow-sm" style="border: 1px solid #E5E7EB;">
        <div class="p-0">
            <?php if (count($logs) > 0): ?>
                <ul class="list-unstyled mb-0" id="auditList">
                    <?php foreach ($logs as $index => $log): 
                        $action = strtolower($log['action_type']);
                        $badge_text = htmlspecialchars($log['action_type']);
                        $badge_style = '';
                        
                        if (str_contains($action, 'approve')) {
                            $badge_style = 'background-color: #ECFDF5; color: #059669;';
                            $badge_text = 'Approved';
                            $category = 'Approvals';
                        } elseif (str_contains($action, 'pass')) {
                            $badge_style = 'background-color: #F5F3FF; color: #7C3AED;';
                            $badge_text = 'Passed';
                            $category = 'Approvals';
                        } elseif (str_contains($action, 'generate')) {
                            $badge_style = 'background-color: #EFF6FF; color: #2563EB;';
                            $badge_text = 'Generated';
                            $category = 'Approvals';
                        } elseif (str_contains($action, 'submit')) {
                            $badge_style = 'background-color: #FAF5FF; color: #9333EA;';
                            $badge_text = 'Submitted';
                            $category = 'Submissions';
                        } elseif (str_contains($action, 'fail') || str_contains($action, 'delete')) {
                            $badge_style = 'background-color: #FEF2F2; color: #DC2626;';
                            $badge_text = str_contains($action, 'fail') ? 'Failed Login Attempt' : 'Deleted';
                            $category = 'Alerts';
                        } elseif (str_contains($action, 'update') || str_contains($action, 'edit')) {
                            $badge_style = 'background-color: #FFFBEB; color: #D97706;';
                            $badge_text = 'Updated section';
                            $category = 'Edits';
                        } elseif (str_contains($action, 'request')) {
                            $badge_style = 'background-color: #ECFDF5; color: #059669;';
                            $badge_text = 'Requested revisions';
                            $category = 'Submissions';
                        } else {
                            $badge_style = 'background-color: #F3F4F6; color: #4B5563;';
                            $category = 'System Logs';
                        }
                        
                        $is_last = $index === count($logs) - 1;
                        $border_bottom = $is_last ? '' : 'border-bottom: 1px solid #F9FAFB;';
                    ?>
                        <li class="activity-item d-flex align-items-center py-3 px-4 position-relative" style="<?= $border_bottom ?>" data-category="<?= $category ?>">
                            <!-- Dot -->
                            <div style="width: 6px; height: 6px; border-radius: 50%; background-color: #D1D5DB; margin-right: 16px; flex-shrink: 0;"></div>
                            
                            <!-- Content -->
                            <div class="flex-grow-1 d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-0" style="font-size: 0.85rem; color: #4B5563;">
                                        <strong style="color: #111827; font-weight: 500;"><?= htmlspecialchars($log['user_name']) ?></strong> 
                                        <?= htmlspecialchars($log['details']) ?>
                                    </p>
                                    <div style="font-size: 0.75rem; color: #9CA3AF; margin-top: 2px;">
                                        <?php
                                            $log_date = strtotime($log['created_at']);
                                            $today = strtotime('today');
                                            $yesterday = strtotime('yesterday');
                                            if ($log_date >= $today) {
                                                echo 'Today ' . date('g:i A', $log_date);
                                            } elseif ($log_date >= $yesterday) {
                                                echo 'Yesterday';
                                            } else {
                                                echo date('M j', $log_date);
                                            }
                                        ?>
                                    </div>
                                </div>
                                
                                <!-- Badge -->
                                <div>
                                    <span class="badge rounded-pill fw-normal" style="<?= $badge_style ?> padding: 4px 10px; font-size: 0.65rem; border: 1px solid rgba(0,0,0,0.03);">
                                        <?= $badge_text ?>
                                    </span>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="text-center py-5">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #F3F4F6; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #9CA3AF; font-size: 1.25rem;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h6 class="fw-medium text-dark mb-1" style="font-size: 0.9rem;">No Activity Logs</h6>
                    <p class="text-muted small mb-0" style="font-size: 0.8rem;">System audit trail is currently empty.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('logSearch');
    const filterBtn = document.getElementById('selectedFilter');
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const logItems = document.querySelectorAll('.activity-item');
    
    let currentCategory = 'All Logs';

    function filterLogs() {
        const q = searchInput.value.toLowerCase();
        
        logItems.forEach(item => {
            const textMatch = item.textContent.toLowerCase().includes(q);
            const catMatch = currentCategory === 'All Logs' || item.getAttribute('data-category') === currentCategory;
            
            if (textMatch && catMatch) {
                item.style.setProperty('display', 'flex', 'important');
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });
    }

    searchInput?.addEventListener('input', filterLogs);
    
    dropdownItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            
            dropdownItems.forEach(di => {
                di.style.backgroundColor = 'transparent';
                di.classList.remove('text-primary');
                const i = di.querySelector('i');
                if(i) i.remove();
            });
            
            item.style.backgroundColor = '#F9FAFB';
            item.classList.add('text-primary');
            item.innerHTML += ' <i class="bi bi-check2"></i>';
            
            currentCategory = item.textContent.trim();
            filterBtn.textContent = currentCategory;
            
            filterLogs();
        });
    });
});
</script>
</body>
</html>
