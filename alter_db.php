<?php
require 'config/db.php';
try {
    $pdo->exec("ALTER TABLE activity_plans ADD COLUMN scheduled_time TIME NULL AFTER scheduled_date");
    echo "Column added successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
