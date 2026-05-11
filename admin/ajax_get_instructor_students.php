<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['instructor_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing instructor_id']);
    exit;
}

$instructor_id = $_GET['instructor_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            s.id AS section_id,
            s.section_name,
            s.component,
            st.student_id,
            st.first_name,
            st.last_name,
            st.course
        FROM sections s
        LEFT JOIN enrollments e ON s.id = e.section_id
        LEFT JOIN students st ON e.student_id = st.student_id
        WHERE s.instructor_id = ?
        ORDER BY s.section_name, st.last_name, st.first_name
    ");
    $stmt->execute([$instructor_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sections = [];
    foreach ($results as $row) {
        $sec_id = $row['section_id'];
        if (!isset($sections[$sec_id])) {
            $sections[$sec_id] = [
                'section_name' => $row['section_name'],
                'component' => $row['component'],
                'students' => []
            ];
        }
        if ($row['student_id']) {
            $sections[$sec_id]['students'][] = [
                'student_id' => $row['student_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'course' => $row['course']
            ];
        }
    }

    echo json_encode(['success' => true, 'sections' => array_values($sections)]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
