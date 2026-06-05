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
        $inlineName = $document['original_name'] ?: basename($absolutePath);
        $inlineName = str_replace(array("\r", "\n", '"'), array('', '', ''), $inlineName);
        header('Content-Type: ' . ($document['file_mime'] ?: 'application/octet-stream'));
        header('Content-Disposition: inline; filename="' . $inlineName . '"');
        header('Content-Length: ' . filesize($absolutePath));
        readfile($absolutePath);
        exit();
    }
}

header('Location: document.php?id=' . $id);
exit();
