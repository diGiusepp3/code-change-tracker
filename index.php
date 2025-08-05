<?php include 'header.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/ini.inc'; ?>

<section>
    <h2>Welcome, Developer</h2>
    <p>Track every code change, compare diffs, and keep control over your projects.</p>

    <a href="/public/admin.php" class="button-primary">View Changes</a>

    <div class="code-block">
        <?php
        // Laatste wijziging ophalen
        $result = $conn->query("SELECT file_path, diff, changed_at FROM code_changes ORDER BY changed_at DESC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Diff highlighten
            $diffLines = explode("\n", $row['diff']);
            $output = '';
            foreach ($diffLines as $line) {
                if (strpos($line, '+') === 0) {
                    $output .= "<span style='color:#00f2fe;'>".htmlspecialchars($line)."</span>\n";
                } elseif (strpos($line, '-') === 0) {
                    $output .= "<span style='color:#ff6b6b;'>".htmlspecialchars($line)."</span>\n";
                } else {
                    $output .= htmlspecialchars($line)."\n";
                }
            }
            echo "<pre><code>";
            echo "# File: " . htmlspecialchars($row['file_path']) . "\n";
            echo "# Changed at: " . htmlspecialchars($row['changed_at']) . "\n\n";
            echo $output;
            echo "</code></pre>";
        } else {
            echo "<pre><code>No changes found yet.</code></pre>";
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>
