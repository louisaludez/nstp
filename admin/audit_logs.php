<?php
session_start();
require '../config/db.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all audit logs
$stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC");
$logs = $stmt->fetchAll();

// Helper function for badges
function getBadgeStyle($action)
{
    if (strpos($action, 'Created') !== false)
        return 'bg-success bg-opacity-10 text-success';
    if (strpos($action, 'Updated') !== false)
        return 'bg-info bg-opacity-10 text-primary';
    if (strpos($action, 'Assigned') !== false)
        return 'bg-purple-light text-purple';
    return 'bg-secondary bg-opacity-10 text-secondary';
}

include '../includes/header.php';
include '../includes/admin_sidebar.php';
?>

<style>
    /* Panel styling - Removed height: 100% to stop the stretching */
    .panel-container {
        background-color: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 24px;
        width: 100%;
    }

    .btn-brand {
        background-color: var(--primary-active, #4A46D6);
        color: white;
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 16px;
    }

    /* Search & Filter Bar */
    .filter-bar {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 4px;
        margin-bottom: 24px;
        background-color: white;
    }

    .search-input {
        border: none;
        box-shadow: none;
        background: transparent;
    }

    .search-input:focus {
        outline: none;
        box-shadow: none;
    }

    .filter-select {
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        padding: 6px 32px 6px 12px;
        font-size: 0.9rem;
        color: #4B5563;
        background-color: white;
    }

    /* Log Cards */
    .log-card {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 12px;
        display: flex;
        align-items: flex-start;
        background-color: white;
    }

    .shield-icon {
        color: var(--primary-active, #4A46D6);
        font-size: 1.2rem;
        margin-right: 16px;
        margin-top: 2px;
    }

    .bg-purple-light {
        background-color: #F3E8FF;
    }

    .text-purple {
        color: #9333EA;
    }

    .action-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 500;
        margin-right: 12px;
    }
</style>

<div class="flex-grow-1 p-5 d-flex flex-column align-items-start">

    <?php include '../includes/topbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 w-100">
        <div>
            <h3 class="fw-bold mb-1" style="color: #111827;">Audit Logs</h3>
            <p class="text-muted mb-0">Track all system changes and user activities</p>
        </div>
        <button type="button" class="btn btn-brand border-0">
            <i class="bi bi-download me-2"></i> Export Logs
        </button>
    </div>

    <div class="panel-container">
        <div class="d-flex justify-content-between align-items-center filter-bar" style="height: 50px;">
            <div class="d-flex align-items-center px-3">
                <i class="bi bi-search text-muted"></i>
                <input type="text" id="logSearch" class="form-control search-input ms-2" placeholder="Search logs...">
            </div>
            <div class="d-flex align-items-center px-3 border-start" style="height: 100%;">
                <i class="bi bi-funnel text-muted me-2"></i>
                <select class="filter-select border-0">
                    <option>All Actions</option>
                    <option>Created Student</option>
                    <option>Updated Grade</option>
                    <option>Assigned Instructor</option>
                </select>
            </div>
        </div>

        <div class="w-100">
            <?php if (count($logs) > 0): ?>
                <?php foreach ($logs as $log): ?>
                    <div class="log-card">
                        <i class="bi bi-shield shield-icon"></i>
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="action-badge <?= getBadgeStyle($log['action_type']) ?>">
                                    <?= htmlspecialchars($log['action_type']) ?>
                                </span>
                                <span class="text-muted small fw-medium"><?= htmlspecialchars($log['user_name']) ?></span>
                            </div>
                            <p class="mb-1" style="color: #111827; font-size: 0.95rem;">
                                <?= htmlspecialchars($log['details']) ?>
                            </p>
                            <small class="text-muted" style="font-size: 0.8rem;">
                                <?= htmlspecialchars($log['created_at']) ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-x text-muted fs-1 mb-2"></i>
                    <p class="text-muted small">No audit logs found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('logSearch').addEventListener('keyup', function () {
        // 1. Get what the user typed and convert to lowercase
        let filter = this.value.toLowerCase();

        // 2. Grab all the log cards
        let cards = document.querySelectorAll('.log-card');

        cards.forEach(card => {
            // 3. Get the text content from the badge, user name, and details
            let text = card.textContent.toLowerCase();

            // 4. If the text matches, show the card; otherwise, hide it
            if (text.includes(filter)) {
                card.style.display = ""; // Shows the card
            } else {
                card.style.display = "none"; // Hides the card
            }
        });
    });

    // Optional: Filter by Action Type dropdown
    document.querySelector('.filter-select').addEventListener('change', function () {
        let selectedAction = this.value.toLowerCase();
        let cards = document.querySelectorAll('.log-card');

        cards.forEach(card => {
            let actionBadge = card.querySelector('.action-badge').textContent.toLowerCase();

            if (selectedAction === "all actions" || actionBadge.includes(selectedAction)) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    });

</script>
</body>

</html>