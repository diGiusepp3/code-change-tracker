<?php
/**
 * CodeDiff - Simple diff engine (line by line)
 *
 * Compares old and new content and produces a diff array and text render.
 */
class CodeDiff {

    /**
     * Generate a diff array between two strings
     *
     * @param string $old Old content
     * @param string $new New content
     * @return array Diff array with 'type' => ('unchanged','added','removed') and 'line'
     */
    public function diff($old, $new) {
        $oldLines = explode("\n", $old);
        $newLines = explode("\n", $new);

        $diff = [];
        $max = max(count($oldLines), count($newLines));

        for ($i = 0; $i < $max; $i++) {
            $oldLine = $oldLines[$i] ?? null;
            $newLine = $newLines[$i] ?? null;

            if ($oldLine === $newLine) {
                $diff[] = ['type' => 'unchanged', 'line' => $oldLine];
            } elseif ($oldLine !== null && $newLine === null) {
                $diff[] = ['type' => 'removed', 'line' => $oldLine];
            } elseif ($oldLine === null && $newLine !== null) {
                $diff[] = ['type' => 'added', 'line' => $newLine];
            } else {
                // Line changed
                $diff[] = ['type' => 'removed', 'line' => $oldLine];
                $diff[] = ['type' => 'added', 'line' => $newLine];
            }
        }

        return $diff;
    }

    /**
     * Render diff as plain text (like git)
     *
     * @param array $diff
     * @return string
     */
    public function renderText(array $diff) {
        $output = '';
        foreach ($diff as $item) {
            switch ($item['type']) {
                case 'added':
                    $output .= "+ {$item['line']}\n";
                    break;
                case 'removed':
                    $output .= "- {$item['line']}\n";
                    break;
                default:
                    $output .= "  {$item['line']}\n";
            }
        }
        return $output;
    }
}
