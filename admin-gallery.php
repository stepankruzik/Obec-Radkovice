<?php
require_once('app.php');
app_require_admin();

if (isset($_POST['save_album'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $editingRecord = $id > 0 ? app_find_by_id('gallery_albums', $id) : null;

    $data = array(
        'title' => app_post('title'),
        'category' => app_post('category'),
        'description' => app_post('description'),
        'event_date' => app_post('event_date', date('Y-m-d')),
        'status' => app_post('status', 'public'),
        'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
        'updated_at' => app_now(),
    );

    $uploadedCover = app_upload_file($_FILES['cover_image'] ?? array(), 'uploads/gallery-covers', array('jpg', 'jpeg', 'png', 'webp', 'gif'));
    if ($uploadedCover) {
        if (!empty($editingRecord['cover_image']) && str_starts_with($editingRecord['cover_image'], 'uploads/')) {
            app_delete_uploaded_file($editingRecord['cover_image']);
        }
        $data['cover_image'] = $uploadedCover['path'];
    } elseif (!$editingRecord) {
        $data['cover_image'] = 'img/uvod.JPG';
    }

    if ($id > 0) {
        Db::update('gallery_albums', $data, 'WHERE id = ?', $id);
        app_refresh_album_count($id);
        app_set_flash('success', 'Album bylo upraveno.');
    } else {
        $data['created_at'] = app_now();
        $data['item_count'] = 0;
        Db::insert('gallery_albums', $data);
        app_set_flash('success', 'Album bylo vytvořeno. Fotky do něj můžete nahrát přes Upravit.');
    }

    app_redirect('admin-gallery.php');
}

if (isset($_POST['upload_photos'])) {
    $albumId = (int) ($_POST['album_id'] ?? 0);
    $album = $albumId > 0 ? app_find_by_id('gallery_albums', $albumId) : null;

    if (!$album) {
        app_set_flash('error', 'Nejprve vytvořte album.');
        app_redirect('admin-gallery.php');
    }

    $files = $_FILES['album_photos'] ?? null;
    $uploadedCount = 0;

    if ($files && is_array($files['name'] ?? null)) {
        for ($i = 0; $i < count($files['name']); $i++) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $file = array(
                'name' => $files['name'][$i],
                'type' => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$i] ?? 0,
            );

            $uploaded = app_upload_file($file, 'uploads/gallery-photos', array('jpg', 'jpeg', 'png', 'webp', 'gif'));
            if (!$uploaded) {
                continue;
            }

            $sortOrder = (int) (Db::querySingle("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM gallery_photos WHERE album_id = ?", $albumId) ?? 1);
            Db::insert('gallery_photos', array(
                'album_id' => $albumId,
                'title' => pathinfo($uploaded['name'], PATHINFO_FILENAME),
                'image_path' => $uploaded['path'],
                'sort_order' => $sortOrder,
                'is_visible' => 1,
                'created_at' => app_now(),
                'updated_at' => app_now(),
            ));
            $uploadedCount++;
        }
    }

    $firstPhoto = Db::queryOne("SELECT image_path FROM gallery_photos WHERE album_id = ? ORDER BY sort_order ASC, id ASC LIMIT 1", $albumId);
    if ($firstPhoto && (empty($album['cover_image']) || $album['cover_image'] === 'img/uvod.JPG')) {
        Db::update('gallery_albums', array('cover_image' => $firstPhoto['image_path'], 'updated_at' => app_now()), 'WHERE id = ?', $albumId);
    }

    app_refresh_album_count($albumId);
    app_set_flash($uploadedCount > 0 ? 'success' : 'error', $uploadedCount > 0 ? 'Fotky byly nahrány.' : 'Nevybrali jste žádné fotky.');
    app_redirect('admin-gallery.php?edit=' . $albumId);
}

if (isset($_POST['delete_photo'])) {
    $photoId = (int) ($_POST['photo_id'] ?? 0);
    $albumId = (int) ($_POST['album_id'] ?? 0);
    $photo = app_find_by_id('gallery_photos', $photoId);
    if ($photo) {
        app_delete_uploaded_file($photo['image_path']);
        app_delete_by_id('gallery_photos', $photoId);
        app_refresh_album_count($albumId);
    }
    app_set_flash('success', 'Fotka byla smazána.');
    app_redirect('admin-gallery.php?edit=' . $albumId);
}

if (isset($_POST['delete_album'])) {
    $albumId = (int) ($_POST['id'] ?? 0);
    $photos = Db::queryAll("SELECT * FROM gallery_photos WHERE album_id = ?", $albumId) ?: array();
    foreach ($photos as $photo) {
        app_delete_uploaded_file($photo['image_path']);
    }

    $album = app_find_by_id('gallery_albums', $albumId);
    if ($album && !empty($album['cover_image']) && str_starts_with($album['cover_image'], 'uploads/')) {
        app_delete_uploaded_file($album['cover_image']);
    }

    Db::query("DELETE FROM gallery_photos WHERE album_id = ?", $albumId);
    app_delete_by_id('gallery_albums', $albumId);
    app_set_flash('success', 'Album bylo smazáno.');
    app_redirect('admin-gallery.php');
}

if (isset($_POST['toggle_album'])) {
    app_toggle_visibility('gallery_albums', (int) ($_POST['id'] ?? 0));
    app_set_flash('success', 'Viditelnost alba byla změněna.');
    app_redirect('admin-gallery.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? app_find_by_id('gallery_albums', $editId) : null;
$albums = app_fetch_all('gallery_albums', 'event_date DESC, updated_at DESC, id DESC');
$albumPhotos = $editing ? (Db::queryAll("SELECT * FROM gallery_photos WHERE album_id = ? ORDER BY sort_order ASC, id ASC", $editId) ?: array()) : array();

$pageTitle = 'Admin | Fotogalerie';
$adminPageTitle = 'Správa fotogalerie';
$adminPageDescription = 'Vytvořte album, nastavte datum a potom do něj nahrajte fotografie.';
$adminActiveNav = 'gallery';
$adminActionLabel = $editing ? 'Upravuji album' : '+ Nové album';
$adminActionHref = 'admin-gallery.php#editor';
require_once('includes/admin-header.php');
?>

            <section class="admin-section-grid">
                <section class="admin-panel">
                    <div class="admin-panel-head">
                        <h2>Alba</h2>
                        <a href="#editor"><?php echo $editing ? 'Editace alba' : 'Nové album'; ?></a>
                    </div>

                    <div class="admin-gallery-grid">
                        <?php foreach ($albums as $album): ?>
                            <article class="admin-gallery-tile">
                                <img src="<?php echo app_e($album['cover_image']); ?>" alt="<?php echo app_e($album['title']); ?>">
                                <div class="admin-gallery-body">
                                    <h3><?php echo app_e($album['title']); ?></h3>
                                    <p><?php echo app_e(date('d. m. Y', strtotime((string) $album['event_date']))); ?> • <?php echo (int) $album['item_count']; ?> fotografií • <?php echo app_gallery_status_label((string) $album['status']); ?></p>
                                    <div class="content-card-actions content-card-actions-row compact-actions">
                                        <a class="action-link" href="admin-gallery.php?edit=<?php echo (int) $album['id']; ?>">Upravit</a>
                                        <a class="action-link" href="album.php?id=<?php echo (int) $album['id']; ?>" target="_blank" rel="noreferrer">Otevřít</a>
                                        <form class="inline-form" method="post">
                                            <input type="hidden" name="id" value="<?php echo (int) $album['id']; ?>">
                                            <button class="action-button action-button-text" type="submit" name="toggle_album"><?php echo (int) $album['is_visible'] === 1 ? 'Skrýt' : 'Zobrazit'; ?></button>
                                        </form>
                                        <form class="inline-form" method="post" onsubmit="return confirm('Opravdu smazat album?');">
                                            <input type="hidden" name="id" value="<?php echo (int) $album['id']; ?>">
                                            <button class="action-button action-button-text" type="submit" name="delete_album">Smazat</button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <form id="editor" class="admin-editor" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo (int) ($editing['id'] ?? 0); ?>">
                        <div class="admin-panel-head">
                            <h2><?php echo $editing ? 'Upravit album' : 'Vytvořit album'; ?></h2>
                        </div>
                        <div class="admin-form-grid">
                            <label class="admin-field">
                                <span>Název alba</span>
                                <input type="text" name="title" value="<?php echo app_e($editing['title'] ?? ''); ?>" required>
                            </label>
                            <label class="admin-field">
                                <span>Kategorie</span>
                                <input type="text" name="category" value="<?php echo app_e($editing['category'] ?? ''); ?>" required>
                            </label>
                            <label class="admin-field">
                                <span>Datum alba</span>
                                <input type="date" name="event_date" value="<?php echo app_e($editing['event_date'] ?? date('Y-m-d')); ?>" required>
                            </label>
                            <label class="admin-field">
                                <span>Stav</span>
                                <select name="status">
                                    <?php foreach (array('public' => 'Veřejné', 'draft' => 'Koncept') as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo (($editing['status'] ?? 'public') === $value) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <div class="admin-field">
                                <span>Obrázek obálky</span>
                                <label class="upload-dropzone upload-dropzone-light" data-upload-dropzone>
                                    <input type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp,.gif" data-upload-input>
                                    <strong>Nahrajte titulní fotku alba</strong>
                                    <span>Když ji nevyberete, použije se první nahraná fotka.</span>
                                    <small data-upload-filename><?php echo !empty($editing['cover_image']) ? basename((string) $editing['cover_image']) : 'Volitelné.'; ?></small>
                                </label>
                            </div>
                            <label class="admin-field admin-field-full">
                                <span>Popis</span>
                                <textarea name="description" rows="5" required><?php echo app_e($editing['description'] ?? ''); ?></textarea>
                            </label>
                            <label class="admin-checkbox">
                                <input type="checkbox" name="is_visible" <?php echo !isset($editing['is_visible']) || (int) $editing['is_visible'] === 1 ? 'checked' : ''; ?>>
                                <span>Zobrazovat na webu</span>
                            </label>
                        </div>
                        <div class="editor-actions">
                            <button class="admin-button" type="submit" name="save_album"><?php echo $editing ? 'Uložit album' : 'Vytvořit album'; ?></button>
                            <?php if ($editing): ?>
                                <a class="secondary-link" href="admin-gallery.php">Zrušit editaci</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if ($editing): ?>
                        <section class="admin-editor">
                            <div class="admin-panel-head">
                                <h2>Fotky v albu</h2>
                                <span class="admin-helper-text"><?php echo (int) $editing['item_count']; ?> fotografií</span>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="album_id" value="<?php echo (int) $editing['id']; ?>">
                                <label class="upload-dropzone" data-upload-dropzone>
                                    <input type="file" name="album_photos[]" accept=".jpg,.jpeg,.png,.webp,.gif" multiple data-upload-input>
                                    <strong>Přetáhněte sem fotky</strong>
                                    <span>Nebo klikněte a vyberte více obrázků najednou.</span>
                                    <small data-upload-filename>Zatím není vybraný žádný soubor.</small>
                                </label>
                                <div class="editor-actions">
                                    <button class="admin-button" type="submit" name="upload_photos">Nahrát fotografie</button>
                                </div>
                            </form>

                            <?php if ($albumPhotos): ?>
                                <div class="admin-upload-summary">
                                    <strong>Nahrané fotografie</strong>
                                    <span>Tady můžete jednotlivé fotky rovnou mazat.</span>
                                </div>
                                <div class="admin-photo-strip">
                                    <?php foreach ($albumPhotos as $photo): ?>
                                        <article class="admin-photo-thumb">
                                            <img src="<?php echo app_e($photo['image_path']); ?>" alt="<?php echo app_e($photo['title']); ?>">
                                            <div class="admin-photo-thumb-body">
                                                <strong><?php echo app_e($photo['title']); ?></strong>
                                                <form method="post" onsubmit="return confirm('Opravdu smazat fotku?');">
                                                    <input type="hidden" name="album_id" value="<?php echo (int) $editing['id']; ?>">
                                                    <input type="hidden" name="photo_id" value="<?php echo (int) $photo['id']; ?>">
                                                    <button class="action-button action-button-text" type="submit" name="delete_photo">Smazat fotku</button>
                                                </form>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="admin-upload-empty">
                                    Po nahrání se tady zobrazí náhledy všech fotek v albu.
                                </div>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>
                </section>

                <aside class="admin-side-stack">
                    <section class="admin-panel">
                        <h2>Jak to funguje</h2>
                        <div class="admin-mini-list">
                            <div><strong>1. Vytvořte album</strong><span>Název, datum, kategorie a popis</span></div>
                            <div><strong>2. Uložte ho</strong><span>Po uložení se vrátíte na přehled se zprávou</span></div>
                            <div><strong>3. Otevřete Upravit</strong><span>V detailu alba nahrajete a smažete jednotlivé fotky</span></div>
                        </div>
                    </section>

                    <section class="admin-panel">
                        <h2>Poslední alba</h2>
                        <div class="admin-mini-list">
                            <?php foreach (array_slice($albums, 0, 5) as $album): ?>
                                <div><strong><?php echo app_e($album['title']); ?></strong><span><?php echo app_e(date('d. m. Y', strtotime((string) $album['event_date']))); ?></span></div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </aside>
            </section>

<?php require_once('includes/admin-footer.php'); ?>
