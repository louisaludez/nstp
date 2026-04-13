<?php
require 'config/db.php';

$sections = [
    ['component' => 'CWTS', 'name' => 'CWTS-A', 'year' => '2026-2027', 'sem' => '1st'],
    ['component' => 'CWTS', 'name' => 'CWTS-B', 'year' => '2026-2027', 'sem' => '1st'],
    ['component' => 'CWTS', 'name' => 'CWTS-D', 'year' => '2026-2027', 'sem' => '1st'],
    ['component' => 'LTS', 'name' => 'LTS-A', 'year' => '2026-2027', 'sem' => '1st'],
    ['component' => 'LTS', 'name' => 'LTS-B', 'year' => '2026-2027', 'sem' => '1st'],
    ['component' => 'ROTC', 'name' => 'ROTC-A', 'year' => '2026-2027', 'sem' => '1st'],
    ['component' => 'ROTC', 'name' => 'ROTC-B', 'year' => '2026-2027', 'sem' => '1st'],
    ['component' => 'ROTC', 'name' => 'ROTC-C', 'year' => '2026-2027', 'sem' => '1st']
];

try {
    $stmt = $pdo->prepare("INSERT INTO sections (component, section_name, school_year, semester) VALUES (?, ?, ?, ?)");
    foreach ($sections as $s) {
        // check if exists
        $check = $pdo->prepare("SELECT id FROM sections WHERE section_name = ?");
        $check->execute([$s['name']]);
        if (!$check->fetch()) {
            $stmt->execute([$s['component'], $s['name'], $s['year'], $s['sem']]);
        }
    }
    echo "Sections created successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
