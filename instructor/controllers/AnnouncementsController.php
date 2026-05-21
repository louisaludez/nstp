<?php
// instructor/controllers/AnnouncementsController.php

$instructor_name = $_SESSION['full_name'];
$instructor_id = $_SESSION['user_id'];

// ── Fetch announcements visible to instructors ───────────────────────────────
// Show announcements targeted at 'All' or 'Instructor', ordered by pinned first, then newest
$stmtAnnouncements = $pdo->prepare("
    SELECT * FROM announcements 
    WHERE target_role IN ('All', 'Instructor')
    ORDER BY is_pinned DESC, created_at DESC
");
$stmtAnnouncements->execute();
$announcements = $stmtAnnouncements->fetchAll();

// ── Helper: generate avatar color from source name ───────────────────────────
// Provides a consistent color per source so avatars look the same across page loads
function getSourceAvatarColor(string $source): string {
    $color_map = [
        'NSTP Office'         => '#3B82F6',
        'Dean\'s Office'      => '#10B981',
        'Program Coordinator' => '#A855F7',
        'Registrar'           => '#F97316',
    ];
    return $color_map[$source] ?? '#6366F1'; // Fallback to indigo
}

// ── Helper: generate initials from source name ───────────────────────────────
function getSourceInitials(string $source): string {
    $words = explode(' ', $source);
    if (count($words) >= 2) {
        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }
    return strtoupper(substr($source, 0, 2));
}

// ── Helper: human-readable relative time ─────────────────────────────────────
function timeAgo(string $datetime): string {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);

    if ($diff->y > 0) return $diff->y . 'y ago';
    if ($diff->m > 0) return $diff->m . 'mo ago';
    if ($diff->d > 1) return date('M j', strtotime($datetime)); // e.g. "May 9"
    if ($diff->d === 1) return 'Yesterday';
    if ($diff->h > 0) return $diff->h . 'h ago';
    if ($diff->i > 0) return $diff->i . 'm ago';
    return 'Just now';
}
