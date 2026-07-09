<?php
require 'config/db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (setting_key VARCHAR(50) PRIMARY KEY, setting_value VARCHAR(255)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $pdo->exec("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('ched_approval', '0');");
    echo 'Settings table created and populated.';
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
