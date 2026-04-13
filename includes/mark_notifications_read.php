<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE (user_id = ? OR role = ?) AND is_read = 0");
$stmt->execute([$user_id, $role]);
?>
