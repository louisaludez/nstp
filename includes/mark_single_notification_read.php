<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id'])) exit;

if (isset($_POST['id'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->execute([$_POST['id']]);
}
?>
