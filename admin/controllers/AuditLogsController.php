<?php
// admin/controllers/AuditLogsController.php

$stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC");
$logs = $stmt->fetchAll();

function getBadgeStyle($action) {
    if (strpos($action, 'Created') !== false) return 'bg-success bg-opacity-10 text-success';
    if (strpos($action, 'Updated') !== false) return 'bg-info bg-opacity-10 text-primary';
    if (strpos($action, 'Assigned') !== false) return 'bg-purple-light text-purple';
    return 'bg-secondary bg-opacity-10 text-secondary';
}
