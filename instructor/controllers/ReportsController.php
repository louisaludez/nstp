<?php
// instructor/controllers/ReportsController.php

$instructor_name = $_SESSION['full_name'];
$instructor_id = $_SESSION['user_id'];
$message = '';
$msgType = '';

// ── Handle POST submissions ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity_plan_id = !empty($_POST['activity_plan_id']) ? (int)$_POST['activity_plan_id'] : null;
    $beneficiaries    = (int)($_POST['beneficiaries'] ?? 0);
    $narrative        = trim($_POST['narrative'] ?? '');

    // Validate required fields
    if (!$activity_plan_id || empty($narrative)) {
        $message = "Please select a linked activity and provide a narrative.";
        $msgType = "danger";
    } else {
        // Verify the activity plan belongs to this instructor and is approved
        $stmtVerify = $pdo->prepare("SELECT id, title, section_id, location, scheduled_date FROM activity_plans WHERE id = ? AND instructor_id = ? AND status = 'Approved'");
        $stmtVerify->execute([$activity_plan_id, $instructor_id]);
        $linked_plan = $stmtVerify->fetch();

        if (!$linked_plan) {
            $message = "Invalid linked activity. Only approved activity plans can be linked.";
            $msgType = "danger";
        } else {
            // Determine status: Draft or Pending (submitted)
            $status = isset($_POST['save_draft']) ? 'Draft' : 'Pending';
            $msg_key = isset($_POST['save_draft']) ? 'drafted' : 'submitted';

            $files_attached = 0;
            if (isset($_FILES['reportFiles']) && !empty($_FILES['reportFiles']['name'][0])) {
                $files_attached = count($_FILES['reportFiles']['name']);
            }

            try {
                $stmt = $pdo->prepare("
                    INSERT INTO accomplishment_reports 
                        (instructor_id, section_id, title, location, completed_date, participants_count, accomplishments, files_attached, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $instructor_id,
                    $linked_plan['section_id'],
                    $linked_plan['title'] . ' Report',
                    $linked_plan['location'],
                    $linked_plan['scheduled_date'],
                    $beneficiaries,
                    $narrative,
                    $files_attached,
                    $status
                ]);
                header("Location: " . basename($_SERVER['PHP_SELF']) . "?msg=$msg_key");
                exit;
            } catch (PDOException $e) {
                $message = "Error saving report: " . $e->getMessage();
                $msgType = "danger";
            }
        }
    }
}

// ── Flash messages ───────────────────────────────────────────────────────────
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'submitted':
            $message = "Report submitted successfully for review.";
            $msgType = "success";
            break;
        case 'drafted':
            $message = "Report saved as draft.";
            $msgType = "info";
            break;
    }
}

// ── Fetch all reports for this instructor (with section info) ────────────────
$stmtReports = $pdo->prepare("
    SELECT ar.*, s.component, s.section_name 
    FROM accomplishment_reports ar
    LEFT JOIN sections s ON ar.section_id = s.id
    WHERE ar.instructor_id = ?
    ORDER BY ar.submitted_date DESC
");
$stmtReports->execute([$instructor_id]);
$reports = $stmtReports->fetchAll();

// ── Fetch approved activity plans for the dropdown ───────────────────────────
$stmtPlans = $pdo->prepare("SELECT id, title FROM activity_plans WHERE instructor_id = ? AND status = 'Approved' ORDER BY title ASC");
$stmtPlans->execute([$instructor_id]);
$approved_plans = $stmtPlans->fetchAll();
