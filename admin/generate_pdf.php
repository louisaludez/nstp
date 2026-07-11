<?php
session_start();
require 'config/db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    die("Unauthorized");
}

$section_name = $_GET['section'] ?? '';
if (empty($section_name)) {
    die("Missing section parameter.");
}

$stmt = $pdo->prepare("
    SELECT 
        s.component,
        s.section_name,
        e.serial_number,
        e.status,
        st.first_name,
        st.last_name,
        st.course
    FROM sections s
    JOIN enrollments e ON s.id = e.section_id
    JOIN students st ON e.student_id = st.student_id
    WHERE s.section_name = ? AND e.serial_number IS NOT NULL
    ORDER BY st.last_name, st.first_name
");
$stmt->execute([$section_name]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($students)) {
    die("No certificates to generate for this section.");
}

$component = $students[0]['component'];
$year = date('Y');

$stmtTmpl = $pdo->prepare("SELECT * FROM certificate_templates WHERE program_type = ? AND is_active = 1 LIMIT 1");
$stmtTmpl->execute([$component]);
$template = $stmtTmpl->fetch(PDO::FETCH_ASSOC);

if (!$template) {
    // Check if there is an 'All Programs' template
    $stmtTmpl = $pdo->prepare("SELECT * FROM certificate_templates WHERE program_type = 'All Programs' AND is_active = 1 LIMIT 1");
    $stmtTmpl->execute();
    $template = $stmtTmpl->fetch(PDO::FETCH_ASSOC);
}

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);

$html = '<html><head><style>
    body { font-family: Helvetica, sans-serif; margin: 0; padding: 0; }
    .page-break { page-break-after: always; }
    .cert-container { width: 100%; height: 100%; text-align: center; padding: 50px; box-sizing: border-box; position: relative; }
    .title { font-size: 36px; font-weight: bold; color: #1E293B; margin-top: 50px; margin-bottom: 20px; text-transform: uppercase; }
    .subtitle { font-size: 16px; color: #475569; margin-bottom: 40px; font-weight: bold; }
    .name { font-size: 48px; font-weight: bold; color: #0F172A; text-transform: uppercase; margin-bottom: 30px; }
    .desc { font-size: 18px; color: #334155; margin-bottom: 50px; line-height: 1.6; max-width: 80%; margin-left: auto; margin-right: auto; }
    .serial { font-size: 14px; color: #94A3B8; text-align: left; position: absolute; bottom: 50px; left: 50px; }
    .sig-block { position: absolute; bottom: 50px; right: 50px; text-align: center; width: 300px; }
    .sig-name { font-size: 20px; font-weight: bold; color: #1E293B; border-bottom: 1px solid #334155; padding-bottom: 5px; text-transform: uppercase; }
    .sig-title { font-size: 14px; color: #64748B; margin-top: 5px; }
    .bg-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.15; object-fit: cover; }
    .default-border { border: 15px solid #1E3A8A; }
</style></head><body>';

foreach ($students as $index => $stu) {
    
    if ($template) {
        $title = $template['header_title'] ?: "CERTIFICATE OF COMPLETION";
        $body = $template['body_statement'] ?: "This is to certify that [STUDENT_NAME] has successfully completed...";
        
        // Replace tags
        $body = str_replace('[STUDENT_NAME]', '<strong>' . htmlspecialchars($stu['first_name'] . ' ' . $stu['last_name']) . '</strong>', $body);
        $body = str_replace('[SECTION]', '<strong>' . htmlspecialchars($stu['section_name']) . '</strong>', $body);
        $body = str_replace('[SCHOOL_YEAR]', '<strong>' . $year . '-' . ($year+1) . '</strong>', $body);
        $body = str_replace('[SERIAL_NO]', '<strong>' . htmlspecialchars($stu['serial_number']) . '</strong>', $body);
        
        $sigName = $template['signatory_name'] ?: "Program Coordinator";
        $sigTitle = $template['signatory_title'] ?: "";
        
        $bgHtml = "";
        if (!empty($template['image_path']) && file_exists('../' . $template['image_path'])) {
            $imgData = base64_encode(file_get_contents('../' . $template['image_path']));
            $mime = mime_content_type('../' . $template['image_path']);
            $bgHtml = '<img src="data:'.$mime.';base64,'.$imgData.'" class="bg-img">';
        }
        
        $borderClass = empty($bgHtml) ? 'default-border' : '';

        $html .= '<div class="cert-container ' . $borderClass . '">';
        $html .= $bgHtml;
        $html .= '<div class="subtitle">REPUBLIC OF THE PHILIPPINES<br><span style="font-size: 20px; color:#1E3A8A;">DAVAO DEL NORTE STATE COLLEGE</span></div>';
        $html .= '<div class="title">' . htmlspecialchars($title) . '</div>';
        $html .= '<div class="desc">' . $body . '</div>';
        
        $html .= '<div class="sig-block">';
        $html .= '<div class="sig-name">' . htmlspecialchars($sigName) . '</div>';
        $html .= '<div class="sig-title">' . htmlspecialchars($sigTitle) . '</div>';
        $html .= '</div>';
        
        $html .= '<div class="serial">S.N. ' . htmlspecialchars($stu['serial_number']) . '</div>';
        $html .= '</div>';
        
    } else {
        // Fallback generic
        $title = $stu['component'] === 'ROTC' ? "MILITARY TRAINING CERTIFICATE" : "CERTIFICATE OF COMPLETION";
        $subtitle = "National Service Training Program - " . $stu['component'];
        
        $html .= '<div class="cert-container default-border">';
        $html .= '<div class="title">' . $title . '</div>';
        $html .= '<div class="subtitle">' . $subtitle . '</div>';
        $html .= '<div class="desc">This is to certify that</div>';
        $html .= '<div class="name"><u>' . htmlspecialchars($stu['first_name'] . ' ' . $stu['last_name']) . '</u></div>';
        $html .= '<div class="desc">has satisfactorily completed the requirements for<br><strong>' . htmlspecialchars($stu['course']) . '</strong> - Section <strong>' . htmlspecialchars($stu['section_name']) . '</strong>.</div>';
        $html .= '<div class="sig-block"><div class="sig-name">Program Coordinator</div></div>';
        $html .= '<div class="serial">Serial No: ' . htmlspecialchars($stu['serial_number']) . '</div>';
        $html .= '</div>';
    }
    
    if ($index < count($students) - 1) {
        $html .= '<div class="page-break"></div>';
    }
}

$html .= '</body></html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream("Certificates_".$section_name.".pdf", ["Attachment" => true]);
?>
