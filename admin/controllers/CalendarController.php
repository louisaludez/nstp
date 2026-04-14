<?php
// admin/controllers/CalendarController.php

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_activity'])) {
    $title = trim($_POST['title']);
    $component = $_POST['component'];
    $date = $_POST['activity_date'];
    $time = $_POST['activity_time'];
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    try {
        $stmt = $pdo->prepare("INSERT INTO activities (title, component, activity_date, activity_time, location, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $component, $date, $time, $location, $description]);
        $message = "Activity successfully scheduled.";
        $msgType = "success";
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $msgType = "danger";
    }
}

$stmtActivities = $pdo->query("SELECT * FROM activities WHERE activity_date >= CURDATE() ORDER BY activity_date ASC, activity_time ASC");
$upcoming_activities = $stmtActivities->fetchAll();

$month = date('m');
$year = date('Y');
$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
$first_day_of_month = date('w', mktime(0, 0, 0, $month, 1, $year));

$stmtDates = $pdo->prepare("SELECT DISTINCT DAY(activity_date) as act_day FROM activities WHERE MONTH(activity_date) = ? AND YEAR(activity_date) = ?");
$stmtDates->execute([$month, $year]);
$active_days = $stmtDates->fetchAll(PDO::FETCH_COLUMN);
