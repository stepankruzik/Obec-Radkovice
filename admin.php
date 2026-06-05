<?php
require_once('app.php');

$documentsCount = (int) (Db::querySingle("SELECT COUNT(*) FROM documents") ?? 0);
$publishedDocuments = (int) (Db::querySingle("SELECT COUNT(*) FROM documents WHERE status = 'published'") ?? 0);
$draftDocuments = (int) (Db::querySingle("SELECT COUNT(*) FROM documents WHERE status = 'draft'") ?? 0);
$galleryCount = (int) (Db::querySingle("SELECT COUNT(*) FROM gallery_albums") ?? 0);
$photoCount = (int) (Db::querySingle("SELECT COUNT(*) FROM gallery_photos") ?? 0);
$usersCount = (int) (Db::querySingle("SELECT COUNT(*) FROM users") ?? 0);
$visibleDocuments = (int) (Db::querySingle("SELECT COUNT(*) FROM documents WHERE is_visible = 1") ?? 0);
$latestDocuments = Db::queryAll("SELECT * FROM documents ORDER BY published_at DESC, id DESC LIMIT 5") ?: array();
$latestAlbums = Db::queryAll("SELECT * FROM gallery_albums ORDER BY updated_at DESC, id DESC LIMIT 3") ?: array();
$latestAlbum = Db::queryOne("SELECT * FROM gallery_albums ORDER BY updated_at DESC, id DESC LIMIT 1");

$pageTitle = 'Administrační panel';
$adminPageTitle = 'Administrační panel';
$adminPageDescription = 'Vítejte zpět, dnes je ' . date('j. n. Y');
$adminActiveNav = 'dashboard';
$adminActionLabel = '+ Přidat dokument';
$adminActionHref = 'admin-documents.php#editor';
require_once('includes/admin-header.php');
?>

            <section class="admin-stats">
                <article class="admin-stat-card">
                    <div class="admin-stat-header">
                        <h2>Dokumenty celkem</h2>
                        <span class="stat-link">Databáze</span>
                    </div>
                    <div class="admin-stat-number"><?php echo $documentsCount; ?> <small>záznamů</small></div>
                    <div class="stat-progress"><span style="width: <?php echo min(100, $documentsCount * 6); ?>%;"></span></div>
                </article>

                <article class="admin-stat-card">
                    <div class="admin-stat-header">
                        <h2>Zveřejněné dokumenty</h2>
                        <span class="stat-trend positive"><?php echo $visibleDocuments; ?> viditelných</span>
                    </div>
                    <div class="admin-stat-number"><?php echo $publishedDocuments; ?> <small>publikováno</small></div>
                    <div class="admin-stat-note"><?php echo $documentsCount > 0 ? (int) round(($publishedDocuments / $documentsCount) * 100) : 0; ?> % všech dokumentů</div>
                </article>

                <article class="admin-stat-card">
                    <div class="admin-stat-header">
                        <h2>Koncepty</h2>
                        <span class="stat-trend negative"><?php echo $draftDocuments; ?> čeká</span>
                    </div>
                    <div class="admin-stat-number"><?php echo $draftDocuments; ?> <small>konceptů</small></div>
                    <div class="admin-stat-note">Rozpracované nebo neveřejné dokumenty</div>
                </article>

                <article class="admin-stat-card">
                    <div class="admin-stat-header">
                        <h2>Fotogalerie</h2>
                        <span class="stat-link"><?php echo $galleryCount; ?> alb</span>
                    </div>
                    <div class="admin-stat-number"><?php echo $photoCount; ?> <small>fotografií</small></div>
                    <div class="admin-stat-note"><?php echo $usersCount; ?> uživatelů má přístup do administrace</div>
                </article>
            </section>

            <section class="admin-content-grid">
                <section class="admin-panel admin-documents">
                    <div class="admin-panel-head">
                        <h2>Poslední dokumenty</h2>
                        <a href="admin-documents.php">Zobrazit vše</a>
                    </div>

                    <div class="admin-table">
                        <div class="admin-table-head">
                            <span>Název dokumentu</span>
                            <span>Kategorie</span>
                            <span>Datum</span>
                            <span>Akce</span>
                        </div>

                        <?php foreach ($latestDocuments as $document): ?>
                            <article class="admin-table-row">
                                <div class="admin-doc-title">
                                    <?php if (!empty($document['preview_image'])): ?>
                                        <img class="mini-doc-preview" src="<?php echo app_e($document['preview_image']); ?>" alt="<?php echo app_e($document['title']); ?>">
                                    <?php else: ?>
                                        <span class="mini-doc-icon"><?php echo app_e($document['file_type'] ?: 'DOC'); ?></span>
                                    <?php endif; ?>
                                    <div class="admin-doc-copy">
                                        <strong><?php echo app_e($document['title']); ?></strong>
                                        <span><?php echo app_e($document['status'] === 'published' ? 'Zveřejněno' : 'Koncept'); ?></span>
                                    </div>
                                </div>
                                <span class="pill pill-blue"><?php echo app_e($document['category']); ?></span>
                                <span><?php echo app_e(date('d. m. Y', strtotime($document['published_at'] ?: $document['created_at']))); ?></span>
                                <div class="row-actions row-actions-forms">
                                    <a class="action-link" href="admin-documents.php?edit=<?php echo (int) $document['id']; ?>" aria-label="Upravit">Upravit</a>
                                    <a class="action-link" href="document-open.php?id=<?php echo (int) $document['id']; ?>" target="_blank" rel="noreferrer" aria-label="Otevřít dokument">Otevřít</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <aside class="admin-side-stack">
                    <section class="admin-panel">
                        <h2>Poslední alba</h2>
                        <div class="admin-mini-list">
                            <?php foreach ($latestAlbums as $album): ?>
                                <div>
                                    <strong><?php echo app_e($album['title']); ?></strong>
                                    <span><?php echo (int) $album['item_count']; ?> fotek</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="admin-panel">
                        <h2>Poslední přidané foto</h2>
                        <?php if ($latestAlbum): ?>
                            <div class="admin-photo-card">
                                <img src="<?php echo app_e($latestAlbum['cover_image']); ?>" alt="<?php echo app_e($latestAlbum['title']); ?>">
                                <span><?php echo app_e($latestAlbum['title']); ?></span>
                            </div>
                        <?php endif; ?>
                    </section>
                </aside>
            </section>

<?php require_once('includes/admin-footer.php'); ?>
