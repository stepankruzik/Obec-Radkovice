<?php
require_once('app.php');

$id = (int) ($_GET['id'] ?? 0);
$document = $id > 0 ? Db::queryOne("SELECT * FROM documents WHERE id = ? AND is_visible = 1", $id) : null;

if (!$document) {
    http_response_code(404);
    echo 'Dokument nebyl nalezen.';
    exit();
}

$previewPath = $document['preview_image'] ?: ($document['file_path'] ?? null);
$isImageDocument = app_is_image_path($document['file_path'] ?? '') || app_is_image_path($previewPath);
$hasReadableFile = !empty($document['file_path']);

$pageTitle = $document['title'] . ' | Dokument';
$activePage = 'uredni-deska';
$bodyClass = 'subpage-body';
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell page-hero">
            <p class="page-kicker"><?php echo app_e($document['category']); ?></p>
            <h1><?php echo app_e($document['title']); ?></h1>
            <p class="page-lead"><?php echo app_e($document['summary']); ?></p>
        </section>

        <section class="shell document-detail-layout">
            <article class="contact-detail-card">
                <h2><?php echo $isImageDocument ? 'Náhled dokumentu' : 'Obsah dokumentu'; ?></h2>

                <?php if ($previewPath && app_is_image_path($previewPath)): ?>
                    <img class="document-poster" src="<?php echo app_e($previewPath); ?>" alt="<?php echo app_e($document['title']); ?>">
                <?php else: ?>
                    <p><?php echo nl2br(app_e($document['summary'])); ?></p>
                <?php endif; ?>

                <div class="document-detail-meta">
                    <div><strong>Publikováno:</strong> <?php echo app_e(date('d. m. Y H:i', strtotime($document['published_at'] ?: $document['created_at']))); ?></div>
                    <div><strong>Formát:</strong> <?php echo app_e($document['file_type']); ?></div>
                    <div><strong>Velikost:</strong> <?php echo app_e($document['file_size']); ?></div>
                    <?php if (!empty($document['original_name'])): ?>
                        <div><strong>Název souboru:</strong> <?php echo app_e($document['original_name']); ?></div>
                    <?php endif; ?>
                </div>
            </article>

            <aside class="office-hours-card">
                <h2>Akce</h2>
                <div class="document-detail-actions">
                    <?php if ($hasReadableFile): ?>
                        <a class="preview-button" href="document-open.php?id=<?php echo (int) $document['id']; ?>" target="_blank" rel="noreferrer">Otevřít v nové kartě</a>
                    <?php endif; ?>
                    <a class="button" href="document-download.php?id=<?php echo (int) $document['id']; ?>">Stáhnout dokument</a>
                    <a class="button button-secondary" href="uredni-deska.php">Zpět na úřední desku</a>
                </div>
            </aside>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
