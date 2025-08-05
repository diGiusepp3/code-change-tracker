<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ini.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/change_tracker.php';

header('Content-Type: application/json');

// --- scan changes (hergebruik jouw scan_changes.php code) ---
require_once $_SERVER['DOCUMENT_ROOT'] . '/public/scan_changes.php';

// --- laatste wijziging ophalen ---
$result = $conn->query("SELECT file_path, diff, changed_at FROM code_changes ORDER BY changed_at DESC LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'file'   => $row['file_path'],
        'changed_at' => $row['changed_at'],
        'diff'   => $row['diff']
    ]);
} else {
    echo json_encode(['status' => 'empty']);
}
