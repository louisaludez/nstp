<?php
// config/db.php

$host = 'localhost';
$dbname = 'nstp_db';
$username = 'root'; // Adjust if your local XAMPP/WAMP uses a different default
$password = '';     // Leave blank for default XAMPP/WAMP, or enter your MySQL password

// utf8mb4 is the modern standard to prevent character encoding errors
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    // Throw an exception when an error occurs so you can easily catch and debug it
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // Always fetch data as an associative array (e.g., $user['full_name'])
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // CRITICAL: Disables emulated prepared statements to force true, secure server-side preparation
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Note: In a live production environment, you would log this to a file instead of showing it to the user
    die("Database connection failed. Please check your XAMPP/MySQL status: " . $e->getMessage());
}
function logAction($pdo, $action_type, $details)
{
    // Grab the name of the Admin currently logged in
    $user_name = $_SESSION['full_name'] ?? 'System Admin';

    $stmt = $pdo->prepare("INSERT INTO audit_logs (action_type, user_name, details) VALUES (?, ?, ?)");
    $stmt->execute([$action_type, $user_name, $details]);
}
?>