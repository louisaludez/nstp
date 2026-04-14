<?php
// instructor/controllers/ActivityPlansController.php

$instructor_name = $_SESSION['full_name'];
$instructor_id = $_SESSION['user_id'];
$message = '';
$msgType = '';

$stmtSec = $pdo->prepare("SELECT id FROM sections WHERE instructor_id = ? LIMIT 1");
$stmtSec->execute([$instructor_id]);
$section_id = $stmtSec->fetchColumn();
$section_id = $section_id ?: null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_plan'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $objectives = $_POST['objectives'];
    $scheduled_date = $_POST['scheduled_date'];
    $scheduled_time = $_POST['scheduled_time'] ?? null;
    $location = $_POST['location'];
    $files_attached = 0;
    if (isset($_FILES['supporting_files']) && !empty($_FILES['supporting_files']['name'][0])) {
        $files_attached = count($_FILES['supporting_files']['name']);
    }
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_plans (instructor_id, section_id, title, description, location, scheduled_date, scheduled_time, objectives, files_attached, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$instructor_id, $section_id, $title, $description, $location, $scheduled_date, $scheduled_time, $objectives, $files_attached]);
        header("Location: activity_plans?msg=submitted");
        exit;
    } catch(PDOException $e) {
        $message = "Error submitting plan: " . $e->getMessage();
        $msgType = "danger";
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'submitted') { $message = "Activity plan submitted successfully and is pending approval."; $msgType = "success"; }
    elseif ($_GET['msg'] === 'deleted') { $message = "Activity plan permanently deleted."; $msgType = "success"; }
}

if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $pdo->prepare("DELETE FROM activity_plans WHERE id = ? AND instructor_id = ?")->execute([$del_id, $instructor_id]);
    header("Location: activity_plans?msg=deleted");
    exit;
}

$stmtPlans = $pdo->prepare("SELECT * FROM activity_plans WHERE instructor_id = ? ORDER BY submitted_date DESC");
$stmtPlans->execute([$instructor_id]);
$plans = $stmtPlans->fetchAll();
