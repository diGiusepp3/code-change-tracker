<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ini.inc';

header('Content-Type: application/json');

$result = $conn->query("SELECT file_path, diff, changed_at FROM code_changes ORDER BY changed_at DESC LIMIT 5");
$changes = [];

while ($row = $result->fetch_assoc()) {
    $changes[] = $row;
}

echo json_encode([
    'status' => 'success',
    'changes' => $changes
]);
