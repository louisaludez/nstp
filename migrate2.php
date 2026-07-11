<?php
require 'config/db.php';
try {
    $pdo->exec('ALTER TABLE certificate_templates 
        ADD COLUMN header_title VARCHAR(255) DEFAULT NULL, 
        ADD COLUMN body_statement TEXT DEFAULT NULL, 
        ADD COLUMN signatory_name VARCHAR(255) DEFAULT NULL, 
        ADD COLUMN signatory_title VARCHAR(255) DEFAULT NULL, 
        ADD COLUMN is_active TINYINT(1) DEFAULT 0');
    echo 'Success';
} catch(Exception $e) {
    echo $e->getMessage();
}
