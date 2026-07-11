<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$section_name = $_GET['section_name'] ?? '';
if (empty($section_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing section_name']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            s.id AS section_id,
            s.component,
            e.id AS enrollment_id,
            st.student_id,
            st.first_name,
            st.last_name
        FROM sections s
        JOIN enrollments e ON s.id = e.section_id
        JOIN students st ON e.student_id = st.student_id
        WHERE s.section_name = ? AND e.status = 'Passed' AND e.serial_number IS NULL
        ORDER BY st.last_name, st.first_name
    ");
    $stmt->execute([$section_name]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $students = [];
    $year = date('Y');
    
    // Get current max serial to make it truly sequential if possible, or just start from a fixed offset
    $stmtMax = $pdo->query("SELECT MAX(CAST(SUBSTRING_INDEX(serial_number, '-', -1) AS UNSIGNED)) as max_s FROM enrollments WHERE serial_number LIKE '%-$year-%'");
    $maxS = $stmtMax->fetchColumn();
    $startSeq = $maxS ? (int)$maxS + 1 : 1;

    foreach ($results as $index => $row) {
        $component = $row['component'];
        $enrollment_id = $row['enrollment_id'];
        
        $cleanSerial = sprintf("%s-%s-%05d", $component, $year, ($startSeq + $index));
        
        $students[] = [
            'enrollment_id' => $enrollment_id,
            'full_name' => $row['last_name'] . ', ' . $row['first_name'],
            'serial_preview' => $cleanSerial
        ];
    }

    echo json_encode(['success' => true, 'component' => $results[0]['component'] ?? '', 'students' => $students]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
