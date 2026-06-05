<?php
require_once('app.php');

$id = (int) ($_GET['id'] ?? 0);
$document = $id > 0 ? Db::queryOne("SELECT * FROM documents WHERE id = ? AND is_visible = 1", $id) : null;

if (!$document) {
    http_response_code(404);
    echo 'Dokument nebyl nalezen.';
    exit();
}

if (!empty($document['file_path'])) {
    $absolutePath = app_public_path($document['file_path']);
    if (is_file($absolutePath)) {
        $downloadName = $document['original_name'] ?: basename($absolutePath);
        $downloadName = str_replace(array("\r", "\n", '"'), array('', '', ''), $downloadName);
        header('Content-Type: ' . ($document['file_mime'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($absolutePath));
        readfile($absolutePath);
        exit();
    }
}

$filename = preg_replace('/[^a-zA-Z0-9\-_]+/', '-', strtolower($document['title'])) . '.txt';
$content = $document['title'] . "\n";
$content .= "Kategorie: " . $document['category'] . "\n";
$content .= "Publikováno: " . date('d. m. Y H:i', strtotime($document['published_at'] ?: $document['created_at'])) . "\n\n";
$content .= $document['summary'] . "\n";

header('Content-Type: text/plain; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($content));

echo $content;
