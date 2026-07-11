<?php
require 'config/db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (setting_key VARCHAR(50) PRIMARY KEY, setting_value VARCHAR(255)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $pdo->exec("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('ched_approval', '0');");
    $pdo->exec("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('grade_scale_range', '1.0 - 5.0');");
    $pdo->exec("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('pass_range', '1.0 - 3.0');");
    $pdo->exec("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('fail_range', '5.0');");
    echo 'Settings table created and populated.';
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
