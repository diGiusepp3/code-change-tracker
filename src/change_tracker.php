<?php
require_once __DIR__ . '/code_diff.php';   // in dezelfde map
require_once $_SERVER['DOCUMENT_ROOT'] . '/ini.inc';

function trackCodeChange($filePath, $newContent, $pageUrl = null, $user = null) {
    global $conn;

    $oldContent = file_exists($filePath) ? file_get_contents($filePath) : '';

    $diffEngine = new CodeDiff(); // <-- werkt nu
    $diff = $diffEngine->diff($oldContent, $newContent);
    $diffText = $diffEngine->renderText($diff);

    $stmt = $conn->prepare(
        "INSERT INTO code_changes (file_path, page_url, diff, changed_by) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('ssss', $filePath, $pageUrl, $diffText, $user);
    $stmt->execute();
    $stmt->close();

    file_put_contents($filePath, $newContent);
    return true;
}
