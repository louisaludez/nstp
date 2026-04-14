<?php
// admin/controllers/SubmissionsController.php

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_plan'])) {
        $plan_id = $_POST['plan_id'];
        $pdo->prepare("UPDATE activity_plans SET status = 'Approved' WHERE id = ?")->execute([$plan_id]);
        $inst_stmt = $pdo->prepare("SELECT instructor_id, title FROM activity_plans WHERE id = ?");
        $inst_stmt->execute([$plan_id]);
        $plan = $inst_stmt->fetch();
        if ($plan) {
            $pdo->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)")
                ->execute([$plan['instructor_id'], "Your activity plan '{$plan['title']}' has been approved.", "activity_plans.php"]);
        }
        $message = "Activity Plan successfully approved.";
        $msgType = "success";
    } elseif (isset($_POST['reject_plan'])) {
        $plan_id = $_POST['plan_id'];
        $pdo->prepare("UPDATE activity_plans SET status = 'Rejected' WHERE id = ?")->execute([$plan_id]);
        $inst_stmt = $pdo->prepare("SELECT instructor_id, title FROM activity_plans WHERE id = ?");
        $inst_stmt->execute([$plan_id]);
        $plan = $inst_stmt->fetch();
        if ($plan) {
            $pdo->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)")
                ->execute([$plan['instructor_id'], "Your activity plan '{$plan['title']}' has been rejected.", "activity_plans.php"]);
        }
        $message = "Activity Plan has been rejected.";
        $msgType = "danger";
    } elseif (isset($_POST['review_report'])) {
        $report_id = $_POST['report_id'];
        $pdo->prepare("UPDATE accomplishment_reports SET status = 'Reviewed' WHERE id = ?")->execute([$report_id]);
        $inst_stmt = $pdo->prepare("SELECT instructor_id, title FROM accomplishment_reports WHERE id = ?");
        $inst_stmt->execute([$report_id]);
        $rep = $inst_stmt->fetch();
        if ($rep) {
            $pdo->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)")
                ->execute([$rep['instructor_id'], "Your accomplishment report '{$rep['title']}' has been reviewed.", "reports.php"]);
        }
        $message = "Accomplishment Report marked as reviewed.";
        $msgType = "success";
    }
}

$stmt_plans = $pdo->query("
    SELECT ap.*, u.full_name as instructor, s.section_name 
    FROM activity_plans ap
    JOIN users u ON ap.instructor_id = u.id
    LEFT JOIN sections s ON ap.section_id = s.id
    ORDER BY ap.submitted_date DESC
");
$activity_plans = $stmt_plans->fetchAll();

$stmt_reps = $pdo->query("
    SELECT ar.*, u.full_name as instructor, s.section_name 
    FROM accomplishment_reports ar
    JOIN users u ON ar.instructor_id = u.id
    LEFT JOIN sections s ON ar.section_id = s.id
    ORDER BY ar.submitted_date DESC
");
$accomplishment_reports = $stmt_reps->fetchAll();

$total_plans = count($activity_plans);
$pending_plans = count(array_filter($activity_plans, function($p) { return $p['status'] === 'Pending'; }));
$approved_plans = count(array_filter($activity_plans, function($p) { return $p['status'] === 'Approved'; }));
$total_reports = count($accomplishment_reports);
