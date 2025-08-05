<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ini.inc';
include $_SERVER['DOCUMENT_ROOT'] . '/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<div class="container">
    <?php if ($id > 0): ?>
        <?php
        $stmt = $conn->prepare("SELECT * FROM code_changes WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $change = $result->fetch_assoc();
        $stmt->close();

        if (!$change) {
            echo "<p>No change found.</p>";
        } else {
            $diffLines = explode("\n", $change['diff']);
            $output = '';
            foreach ($diffLines as $index => $line) {
                $lineNum = $index + 1;
                $escaped = htmlspecialchars($line);

                if (strpos($line, '+') === 0) {
                    $output .= "<span class='diff-added'><span class='line-num'>$lineNum</span> $escaped</span>\n";
                } elseif (strpos($line, '-') === 0) {
                    $output .= "<span class='diff-removed'><span class='line-num'>$lineNum</span> $escaped</span>\n";
                } else {
                    $output .= "<span class='diff-line'><span class='line-num'>$lineNum</span> $escaped</span>\n";
                }
            }
            ?>
            <h2>Diff voor <?= htmlspecialchars($change['file_path']); ?></h2>
            <p><strong>Changed by:</strong> <?= htmlspecialchars($change['changed_by']); ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($change['changed_at']); ?></p>
            <div class="diff"><pre><?= $output; ?></pre></div>
            <p><a href="admin.php">&larr; Back to list</a></p>
        <?php } ?>
    <?php else: ?>
        <?php
        $result = $conn->query("SELECT * FROM code_changes ORDER BY changed_at DESC LIMIT 20");
        ?>
        <table class="diff-table">
            <tr>
                <th>ID</th>
                <th>File</th>
                <th>Changed By</th>
                <th>Date</th>
                <th></th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['file_path']); ?></td>
                    <td><?= htmlspecialchars($row['changed_by']); ?></td>
                    <td><?= htmlspecialchars($row['changed_at']); ?></td>
                    <td>
                        <a href="admin.php?id=<?= $row['id']; ?>">View Diff</a> |
                        <a href="versions.php?file=<?= urlencode($row['file_path']); ?>">Bekijk versies</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; ?>
