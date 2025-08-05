<?php
namespace CodeChangeLogger;

use mysqli;

require_once __DIR__ . '/CodeDiff.php';

class ChangeTracker {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    /**
     * Track a change: compute diff, store in DB, overwrite file.
     *
     * @param string      $filePath   Absolute path to the file.
     * @param string      $newContent New file contents.
     * @param string|null $pageUrl    Optional URL context.
     * @param string|null $user       Optional user identifier.
     * @return bool
     */
    public function trackCodeChange(string $filePath, string $newContent, ?string $pageUrl = null, ?string $user = null): bool {
        $oldContent = file_exists($filePath) ? file_get_contents($filePath) : '';
        $diffEngine = new CodeDiff();
        $diff = $diffEngine->diff($oldContent, $newContent);
        $diffText = $diffEngine->renderText($diff);

        $stmt = $this->conn->prepare(
            "INSERT INTO code_changes (file_path, page_url, diff, changed_by) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('ssss', $filePath, $pageUrl, $diffText, $user);
        $stmt->execute();
        $stmt->close();

        file_put_contents($filePath, $newContent);
        return true;
    }
}
