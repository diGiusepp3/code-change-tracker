<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ini.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/change_tracker.php';

$rootDir = realpath($_SERVER['DOCUMENT_ROOT']);
$excludeDirs = ['logs', 'vendor', '.git', 'node_modules'];

// Alle bestanden scannen
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir));

foreach ($rii as $file) {
    if ($file->isDir()) continue;

    $filePath = $file->getPathname();
    foreach ($excludeDirs as $ex) {
        if (strpos($filePath, "/$ex") !== false) continue 2;
    }

    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php', 'js', 'css', 'html'])) continue;

    $relativePath = str_replace($rootDir, '', $filePath);
    $newContent = file_get_contents($filePath);
    $hash = hash('sha256', $newContent);

    // Hash ophalen
    $stmt = $conn->prepare("SELECT file_hash FROM file_hashes WHERE file_path = ?");
    $stmt->bind_param('s', $relativePath);
    $stmt->execute();
    $stmt->bind_result($oldHash);
    $stmt->fetch();
    $stmt->close();

    // Eerste keer opslaan
    if ($oldHash === null) {
        $stmt = $conn->prepare("INSERT INTO file_hashes (file_path, file_hash) VALUES (?, ?)");
        $stmt->bind_param('ss', $relativePath, $hash);
        $stmt->execute();
        $stmt->close();

        // Eerste versie opslaan in file_versions
        $stmt = $conn->prepare("INSERT INTO file_versions (file_path, content) VALUES (?, ?)");
        $stmt->bind_param('ss', $relativePath, $newContent);
        $stmt->execute();
        $stmt->close();
        continue;
    }

    // Als er verschil is
    if ($hash !== $oldHash) {
        // Laatste versie ophalen
        $stmt = $conn->prepare("SELECT content FROM file_versions WHERE file_path = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param('s', $relativePath);
        $stmt->execute();
        $stmt->bind_result($oldContent);
        $stmt->fetch();
        $stmt->close();

        // Diff maken
        $diffEngine = new CodeDiff();
        $diff = $diffEngine->diff($oldContent ?? '', $newContent);
        $diffText = $diffEngine->renderText($diff);

        // Diff opslaan
        $stmt = $conn->prepare("INSERT INTO code_changes (file_path, diff, changed_by) VALUES (?, ?, ?)");
        $changedBy = "AutoScanner";
        $stmt->bind_param('sss', $relativePath, $diffText, $changedBy);
        $stmt->execute();
        $stmt->close();

        // Nieuwe versie opslaan
        $stmt = $conn->prepare("INSERT INTO file_versions (file_path, content) VALUES (?, ?)");
        $stmt->bind_param('ss', $relativePath, $newContent);
        $stmt->execute();
        $stmt->close();

        // Hash bijwerken
        $stmt = $conn->prepare("UPDATE file_hashes SET file_hash = ? WHERE file_path = ?");
        $stmt->bind_param('ss', $hash, $relativePath);
        $stmt->execute();
        $stmt->close();
    }
}

echo "Scan completed at " . date('Y-m-d H:i:s');
