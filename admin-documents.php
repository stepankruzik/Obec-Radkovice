<?php
require_once('app.php');
app_require_admin();

if (isset($_POST['save_document'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $editingRecord = $id > 0 ? app_find_by_id('documents', $id) : null;

    try {
        $status = app_post('status', 'published');
        if (!in_array($status, array('published', 'draft'), true)) {
            $status = 'published';
        }

        $data = array(
            'title' => app_post('title'),
            'category' => app_post('category'),
            'status' => $status,
            'summary' => app_post('summary'),
            'published_at' => app_post('published_at') !== '' ? app_post('published_at') . ' 00:00:00' : null,
            'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
            'updated_at' => app_now(),
        );

        $uploadedFile = app_upload_file($_FILES['document_file'] ?? array(), 'uploads/documents', array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'jpg', 'jpeg', 'png', 'webp'));
        $uploadedPreview = app_upload_file($_FILES['preview_image'] ?? array(), 'uploads/document-previews', array('jpg', 'jpeg', 'png', 'webp', 'gif'));

        if ($uploadedFile) {
            app_delete_uploaded_file($editingRecord['file_path'] ?? null);

            $data['file_path'] = $uploadedFile['path'];
            $data['original_name'] = $uploadedFile['name'];
            $data['file_mime'] = $uploadedFile['mime'];
            $data['file_size'] = $uploadedFile['size'];
            $data['file_type'] = $uploadedFile['extension'];

            if (app_is_image_path($uploadedFile['path']) && !$uploadedPreview) {
                $data['preview_image'] = $uploadedFile['path'];
            } elseif (($editingRecord['preview_image'] ?? '') === ($editingRecord['file_path'] ?? '')) {
                $data['preview_image'] = null;
            }
        }

        if ($uploadedPreview) {
            if (!empty($editingRecord['preview_image']) && ($editingRecord['preview_image'] !== ($editingRecord['file_path'] ?? null))) {
                app_delete_uploaded_file($editingRecord['preview_image']);
            }

            $data['preview_image'] = $uploadedPreview['path'];
        }

        if (!$editingRecord && !$uploadedFile) {
            throw new RuntimeException('Vyberte soubor dokumentu nebo plakátu.');
        }

        if ($id > 0) {
            Db::update('documents', $data, 'WHERE id = ?', $id);
            app_set_flash('success', 'Dokument byl upraven.');
        } else {
            $data['created_at'] = app_now();
            Db::insert('documents', $data);
            app_set_flash('success', 'Dokument byl přidán.');
        }
    } catch (RuntimeException $exception) {
        app_set_flash('error', $exception->getMessage());
        $redirect = 'admin-documents.php';
        if ($id > 0) {
            $redirect .= '?edit=' . $id . '#editor';
        } else {
            $redirect .= '#editor';
        }
        app_redirect($redirect);
    }

    app_redirect('admin-documents.php');
}

if (isset($_POST['delete_document'])) {
    $documentToDelete = app_find_by_id('documents', (int) ($_POST['id'] ?? 0));
    if ($documentToDelete) {
        app_delete_uploaded_file($documentToDelete['file_path'] ?? null);
        if (($documentToDelete['preview_image'] ?? '') !== ($documentToDelete['file_path'] ?? '')) {
            app_delete_uploaded_file($documentToDelete['preview_image'] ?? null);
        }
        app_delete_by_id('documents', (int) $documentToDelete['id']);
    }
    app_set_flash('success', 'Dokument byl smazán.');
    app_redirect('admin-documents.php');
}

if (isset($_POST['toggle_document'])) {
    app_toggle_visibility('documents', (int) ($_POST['id'] ?? 0));
    app_set_flash('success', 'Viditelnost dokumentu byla změněna.');
    app_redirect('admin-documents.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? app_find_by_id('documents', $editId) : null;
$search = trim($_GET['q'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

if ($search !== '') {
    $searchLike = '%' . $search . '%';
    $totalDocuments = (int) (Db::querySingle("SELECT COUNT(*) FROM documents WHERE title LIKE ? OR category LIKE ?", $searchLike, $searchLike) ?? 0);
    $documents = Db::queryAll(
        "SELECT * FROM documents WHERE title LIKE ? OR category LIKE ? ORDER BY published_at DESC, id DESC LIMIT $perPage OFFSET $offset",
        $searchLike,
        $searchLike
    ) ?: array();
} else {
    $totalDocuments = (int) (Db::querySingle("SELECT COUNT(*) FROM documents") ?? 0);
    $documents = Db::queryAll("SELECT * FROM documents ORDER BY published_at DESC, id DESC LIMIT $perPage OFFSET $offset") ?: array();
}

$totalPages = max(1, (int) ceil($totalDocuments / $perPage));
$categoryCounts = Db::queryAll("SELECT category, COUNT(*) AS total FROM documents GROUP BY category ORDER BY total DESC") ?: array();
$drafts = Db::queryAll("SELECT title, published_at FROM documents WHERE status = 'draft' ORDER BY published_at ASC, id DESC LIMIT 5") ?: array();

$pageTitle = 'Admin | Dokumenty';
$adminPageTitle = 'Správa dokumentů';
$adminPageDescription = 'Přehled úředních dokumentů, upload souborů a publikace na web.';
$adminActiveNav = 'documents';
$adminActionLabel = $editing ? 'Upravuji dokument' : '+ Přidat dokument';
$adminActionHref = '#editor';
require_once('includes/admin-header.php');
?>

            <section class="admin-section-grid">
                <section class="admin-panel">
                    <div class="admin-panel-head">
                        <h2>Dokumenty</h2>
                        <a href="#editor"><?php echo $editing ? 'Editace dokumentu' : 'Nový dokument'; ?></a>
                    </div>

                    <div class="admin-table-meta">
                        <span>Zobrazeno <?php echo count($documents); ?> z <?php echo $totalDocuments; ?> dokumentů</span>
                    </div>

                    <div class="admin-table">
                        <div class="admin-table-head">
                            <span>Název dokumentu</span>
                            <span>Kategorie</span>
                            <span>Datum</span>
                            <span>Akce</span>
                        </div>

                        <?php foreach ($documents as $document): ?>
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
                                <div class="row-actions row-actions-stack">
                                    <a class="action-link" href="admin-documents.php?edit=<?php echo (int) $document['id']; ?>#editor" aria-label="Upravit dokument">Upravit</a>
                                    <a class="action-link" href="document-open.php?id=<?php echo (int) $document['id']; ?>" target="_blank" rel="noreferrer" aria-label="Otevřít dokument">Otevřít</a>
                                    <form class="inline-form" method="post">
                                        <input type="hidden" name="id" value="<?php echo (int) $document['id']; ?>">
                                        <button class="action-button action-button-text" type="submit" name="toggle_document" aria-label="Přepnout viditelnost"><?php echo (int) $document['is_visible'] === 1 ? 'Skrýt' : 'Zobrazit'; ?></button>
                                    </form>
                                    <form class="inline-form" method="post" onsubmit="return confirm('Opravdu smazat dokument?');">
                                        <input type="hidden" name="id" value="<?php echo (int) $document['id']; ?>">
                                        <button class="action-button action-button-text" type="submit" name="delete_document" aria-label="Smazat dokument">Smazat</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav class="admin-pagination" aria-label="Stránkování dokumentů">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php $pageUrl = 'admin-documents.php?page=' . $i . ($search !== '' ? '&q=' . urlencode($search) : ''); ?>
                                <a class="admin-page-link <?php echo $i === $page ? 'active' : ''; ?>" href="<?php echo app_e($pageUrl); ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    <?php endif; ?>

                    <form id="editor" class="admin-editor" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo (int) ($editing['id'] ?? 0); ?>">
                        <div class="admin-panel-head">
                            <h2><?php echo $editing ? 'Upravit dokument' : 'Přidat dokument'; ?></h2>
                        </div>
                        <div class="admin-form-grid">
                            <label class="admin-field">
                                <span>Nadpis</span>
                                <input type="text" name="title" value="<?php echo app_e($editing['title'] ?? ''); ?>" required>
                            </label>
                            <label class="admin-field">
                                <span>Kategorie</span>
                                <input type="text" name="category" value="<?php echo app_e($editing['category'] ?? ''); ?>" required>
                            </label>
                            <label class="admin-field">
                                <span>Stav</span>
                                <select name="status">
                                    <?php foreach (array('published' => 'Zveřejněno', 'draft' => 'Koncept') as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo (($editing['status'] ?? 'published') === $value) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label class="admin-field">
                                <span>Publikovat</span>
                                <input type="date" name="published_at" value="<?php echo isset($editing['published_at']) && $editing['published_at'] ? date('Y-m-d', strtotime($editing['published_at'])) : ''; ?>">
                            </label>

                            <div class="admin-field admin-field-full">
                                <span>Soubor dokumentu</span>
                                <label class="upload-dropzone" data-upload-dropzone>
                                    <input type="file" name="document_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.jpg,.jpeg,.png,.webp" data-upload-input>
                                    <strong>Přetáhněte soubor sem</strong>
                                    <span>Nebo klikněte a vyberte PDF, Word, ZIP nebo obrázek/plakát.</span>
                                    <small data-upload-filename>
                                        <?php echo !empty($editing['original_name']) ? app_e($editing['original_name']) : 'Zatím není vybraný žádný soubor.'; ?>
                                    </small>
                                </label>
                            </div>

                            <div class="admin-field admin-field-full">
                                <span>Náhledový obrázek</span>
                                <label class="upload-dropzone upload-dropzone-light" data-upload-dropzone>
                                    <input type="file" name="preview_image" accept=".jpg,.jpeg,.png,.webp,.gif" data-upload-input>
                                    <strong>Nahrajte plakát nebo náhled</strong>
                                    <span>Když je dokument jen obrázek, náhled se použije automaticky.</span>
                                    <small data-upload-filename>
                                        <?php echo !empty($editing['preview_image']) ? basename((string) $editing['preview_image']) : 'Volitelné.'; ?>
                                    </small>
                                </label>
                            </div>

                            <?php if (!empty($editing['preview_image'])): ?>
                                <div class="admin-field admin-field-full">
                                    <span>Aktuální náhled</span>
                                    <img class="editor-preview-image" src="<?php echo app_e($editing['preview_image']); ?>" alt="<?php echo app_e($editing['title'] ?? 'Náhled dokumentu'); ?>">
                                </div>
                            <?php endif; ?>

                            <label class="admin-field admin-field-full">
                                <span>Popis</span>
                                <textarea name="summary" rows="4" placeholder="Krátký popis dokumentu nebo informace k plakátu."><?php echo app_e($editing['summary'] ?? ''); ?></textarea>
                            </label>

                            <label class="admin-checkbox">
                                <input type="checkbox" name="is_visible" <?php echo !isset($editing['is_visible']) || (int) $editing['is_visible'] === 1 ? 'checked' : ''; ?>>
                                <span>Zobrazovat na webu</span>
                            </label>
                        </div>
                        <div class="editor-actions">
                            <button class="admin-button" type="submit" name="save_document">Uložit dokument</button>
                            <?php if ($editing): ?>
                                <a class="secondary-link" href="admin-documents.php">Zrušit editaci</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </section>

                <aside class="admin-side-stack">
                    <section class="admin-panel">
                        <h2>Kategorie</h2>
                        <div class="admin-mini-list">
                            <?php foreach ($categoryCounts as $category): ?>
                                <div><strong><?php echo app_e($category['category']); ?></strong><span><?php echo (int) $category['total']; ?> dokumentů</span></div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="admin-panel">
                        <h2>Koncepty</h2>
                        <div class="admin-mini-list">
                            <?php foreach ($drafts as $item): ?>
                                <div><strong><?php echo app_e($item['title']); ?></strong><span><?php echo app_e(date('d. m. Y', strtotime($item['published_at'] ?: app_now()))); ?></span></div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </aside>
            </section>

<?php require_once('includes/admin-footer.php'); ?>
