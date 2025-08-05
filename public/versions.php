<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ini.inc';

$file = isset($_GET['file']) ? $_GET['file'] : '';
if (!$file) {
    die("Geen bestand opgegeven");
}

$stmt = $conn->prepare("SELECT id, content, created_at FROM file_versions WHERE file_path = ? ORDER BY created_at DESC");
$stmt->bind_param('s', $file);
$stmt->execute();
$result = $stmt->get_result();
?>
    <!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="UTF-8">
        <title>Versies van <?php echo htmlspecialchars($file); ?></title>
        <link rel="stylesheet" href="/assets/styles.css">
    </head>
    <body>
    <h2>Versies van <?php echo htmlspecialchars($file); ?></h2>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<h3>Versie op {$row['created_at']}</h3>";
        echo "<pre><code>" . htmlspecialchars($row['content']) . "</code></pre><hr>";
    }
    ?>
    <a href="admin.php">← Terug</a>
    </body>
    </html>
<?php
