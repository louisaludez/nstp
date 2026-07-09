<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the raw JSON body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($data['scales']) && is_array($data['scales'])) {
        try {
            $pdo->beginTransaction();

            // Clear old scales
            $pdo->exec("DELETE FROM grade_scaling");

            // Insert new scales
            $stmt = $pdo->prepare("INSERT INTO grade_scaling (min_score, max_score, final_grade) VALUES (?, ?, ?)");
            foreach ($data['scales'] as $scale) {
                if (!empty($scale['min']) && !empty($scale['max']) && !empty($scale['grade'])) {
                    $stmt->execute([$scale['min'], $scale['max'], $scale['grade']]);
                }
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Grade scaling configuration saved successfully!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to save configuration: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid data format']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
