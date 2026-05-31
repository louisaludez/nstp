<?php
try {
    $dsn = "mysql:host=localhost;dbname=nstp_db;charset=utf8mb4";
    $pdo = new PDO($dsn, "root", "123456789", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    $sql = "CREATE TABLE IF NOT EXISTS `certificate_templates` (
      `id` int NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `program_type` enum('All Programs','CWTS/LTS','ROTC') NOT NULL,
      `badge_color` enum('Indigo','Emerald','Amber','Rose') NOT NULL,
      `image_path` varchar(255) NOT NULL,
      `status` varchar(50) DEFAULT 'Active',
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($sql);
    echo "Table 'certificate_templates' created successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
