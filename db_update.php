<?php
require 'config/db.php';

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN signature_path VARCHAR(255) DEFAULT NULL;");
    echo "Added signature_path to users table.\n";
} catch (PDOException $e) {
    echo "Error or already exists (signature_path): " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE sections ADD COLUMN max_capacity INT DEFAULT 50;");
    echo "Added max_capacity to sections table.\n";
} catch (PDOException $e) {
    echo "Error or already exists (max_capacity): " . $e->getMessage() . "\n";
}

// Ensure grading scale table exists
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS grade_scales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            min_score DECIMAL(5,2) NOT NULL,
            max_score DECIMAL(5,2) NOT NULL,
            grade_equivalent VARCHAR(10) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Created grade_scales table.\n";
} catch (PDOException $e) {
    echo "Error creating grade_scales: " . $e->getMessage() . "\n";
}
