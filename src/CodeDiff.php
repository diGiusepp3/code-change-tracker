<?php
namespace CodeChangeLogger;

class CodeDiff {
    /**
     * Generate a diff array between two strings
     *
     * @param string $old
     * @param string $new
     * @return array
     */
    public function diff(string $old, string $new): array {
        $oldLines = explode("\n", $old);
        $newLines = explode("\n", $new);
        $diff = [];
        $max = max(count($oldLines), count($newLines));
        for ($i = 0; $i < $max; $i++) {
            $o = $oldLines[$i] ?? null;
            $n = $newLines[$i] ?? null;
            if ($o === $n) {
                $diff[] = ['type'=>'unchanged','line'=>$o];
            } elseif ($o !== null && $n === null) {
                $diff[] = ['type'=>'removed','line'=>$o];
            } elseif ($o === null && $n !== null) {
                $diff[] = ['type'=>'added','line'=>$n];
            } else {
                $diff[] = ['type'=>'removed','line'=>$o];
                $diff[] = ['type'=>'added','line'=>$n];
            }
        }
        return $diff;
    }

    /**
     * Render a diff array to plain-text “git-style”
     *
     * @param array $diff
     * @return string
     */
    public function renderText(array $diff): string {
        $out = '';
        foreach ($diff as $item) {
            switch ($item['type']) {
                case 'added':   $out .= "+ {$item['line']}\n"; break;
                case 'removed': $out .= "- {$item['line']}\n"; break;
                default:        $out .= "  {$item['line']}\n"; break;
            }
        }
        return $out;
    }
}
