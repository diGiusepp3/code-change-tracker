<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ini.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/change_tracker.php';

// Testbestand (zorg dat dit pad schrijfbaar is)
$filePath = $_SERVER['DOCUMENT_ROOT'] . '/testfile.txt';

// Nieuwe inhoud die we willen opslaan
$newContent = "Hello World!\nThis is a new version of the file.\nTimestamp: " . date('Y-m-d H:i:s');

// Pagina URL (optioneel)
$pageUrl = "/public/test_change.php";

// Wie heeft dit gedaan
$user = "TestUser";

// Functie aanroepen
$result = trackCodeChange($filePath, $newContent, $pageUrl, $user);

if ($result) {
    echo "<p>Wijziging succesvol opgeslagen!</p>";
    echo "<p><a href='/public/admin.php'>Bekijk wijzigingen</a></p>";
} else {
    echo "<p>Er is iets misgegaan.</p>";
}
