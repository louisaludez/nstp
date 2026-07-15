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
                        
                        if (str_contains($action, 'create') || str_contains($action, 'update') || str_contains($action, 'edit') || str_contains($action, 'delete')) {
                            $badge_text = 'Edit';
                            $category = 'Edits';
                            $dot_color = '#F59E0B'; // Yellow
                        } else {
                            $badge_text = 'System';
                            $category = 'System Logs';
                            $dot_color = '#8B5CF6'; // Purple
                        }
                        
                        $is_last = $index === count($logs) - 1;
                        $border_bottom = $is_last ? '' : 'border-bottom: 1px solid #F3F4F6;';
                    ?>
                        <li class="activity-item d-flex align-items-start py-4 px-4 position-relative" style="<?= $border_bottom ?>" data-category="<?= $category ?>">
                            <!-- Dot -->
                            <div style="width: 7px; height: 7px; border-radius: 50%; background-color: <?= $dot_color ?>; margin-top: 6px; margin-right: 16px; flex-shrink: 0;"></div>
                            
                            <!-- Content -->
                            <div class="flex-grow-1 d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="mb-1" style="font-size: 0.9rem; color: #6B7280; line-height: 1.4;">
                                        <strong style="color: #374151; font-weight: 600;"><?= htmlspecialchars($log['user_name']) ?></strong> 
                                        <?= htmlspecialchars(strtolower($log['action_type'])) ?>
                                    </p>
                                    <div style="font-size: 0.82rem; color: #9CA3AF; font-style: italic; margin-bottom: 4px;">
                                        <?= htmlspecialchars($log['details']) ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #9CA3AF;">
                                        <?php
                                            $log_date = strtotime($log['created_at']);
                                            echo date('M j, Y g:i A', $log_date);
                                        ?>
                                    </div>
                                </div>
                                
                                <!-- Badge -->
                                <div style="margin-top: 2px;">
                                    <span class="badge rounded-pill fw-medium" style="background-color: #ffffff; color: #4B5563; border: 1px solid #E5E7EB; padding: 5px 14px; font-size: 0.72rem; letter-spacing: 0.2px;">
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
