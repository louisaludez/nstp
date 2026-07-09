<?php
// instructor/controllers/ActivityPlansController.php

$instructor_name = $_SESSION['full_name'];
$instructor_id = $_SESSION['user_id'];
$message = '';
$msgType = '';

// ── Handle POST submissions ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title          = trim($_POST['title'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $objectives     = trim($_POST['objectives'] ?? '');
    $scheduled_date = $_POST['scheduled_date'] ?? null;
    $scheduled_time = $_POST['scheduled_time'] ?? null;
    $location       = trim($_POST['location'] ?? 'TBA');
    $section_id     = !empty($_POST['section_id']) ? (int)$_POST['section_id'] : null;


    // Basic server-side validation
    if (empty($title) || empty($scheduled_date) || empty($objectives)) {
        $message = "Please fill in all required fields (Title, Date, Objectives).";
        $msgType = "danger";
    } else {
        // Verify the section belongs to this instructor (prevent tampering)
        if ($section_id) {
            $stmtVerify = $pdo->prepare("SELECT id FROM sections WHERE id = ? AND instructor_id = ?");
            $stmtVerify->execute([$section_id, $instructor_id]);
            if (!$stmtVerify->fetch()) {
                $section_id = null; // Reset if invalid
            }
        }

        $files_attached = 0;
        if (isset($_FILES['supporting_files']) && !empty($_FILES['supporting_files']['name'][0])) {
            $files_attached = count($_FILES['supporting_files']['name']);
        }

        // Determine status: Draft or Pending (submitted for approval)
        $status = isset($_POST['save_draft']) ? 'Draft' : 'Pending';
        $msg_key = isset($_POST['save_draft']) ? 'drafted' : 'submitted';

        try {
            $stmt = $pdo->prepare("
                INSERT INTO activity_plans 
                    (instructor_id, section_id, title, description, location, scheduled_date, scheduled_time, objectives, files_attached, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $instructor_id, $section_id, $title, $description,
                $location, $scheduled_date, $scheduled_time,
                $objectives, $files_attached, $status
            ]);

            $plan_id = $pdo->lastInsertId();
            
            if ($files_attached > 0) {
                $upload_dir = '../uploads/activity_plans/' . $plan_id . '/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                foreach ($_FILES['supporting_files']['name'] as $key => $name) {
                    $tmp_name = $_FILES['supporting_files']['tmp_name'][$key];
                    $basename = basename($name);
                    move_uploaded_file($tmp_name, $upload_dir . $basename);
                }
            }

            header("Location: " . basename($_SERVER['PHP_SELF']) . "?msg=$msg_key");
            exit;
        } catch (PDOException $e) {
            $message = "Error saving plan: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// ── Handle delete via GET ────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    // Only allow deleting own plans that are in Draft status
    $pdo->prepare("DELETE FROM activity_plans WHERE id = ? AND instructor_id = ? AND status = 'Draft'")
        ->execute([$del_id, $instructor_id]);
    header("Location: " . basename($_SERVER['PHP_SELF']) . "?msg=deleted");
    exit;
}

// ── Flash messages ───────────────────────────────────────────────────────────
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'submitted':
            $message = "Activity plan submitted successfully and is pending approval.";
            $msgType = "success";
            break;
        case 'drafted':
            $message = "Activity plan saved as draft.";
            $msgType = "info";
            break;
        case 'deleted':
            $message = "Activity plan deleted.";
            $msgType = "success";
            break;
    }
}

// ── Fetch all plans for this instructor ──────────────────────────────────────
$stmtPlans = $pdo->prepare("SELECT * FROM activity_plans WHERE instructor_id = ? ORDER BY submitted_date DESC");
$stmtPlans->execute([$instructor_id]);
$plans = $stmtPlans->fetchAll();
