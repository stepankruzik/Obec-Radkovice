<?php
require_once('app.php');

$id = (int) ($_GET['id'] ?? 0);
$album = $id > 0 ? Db::queryOne("SELECT * FROM gallery_albums WHERE id = ? AND is_visible = 1", $id) : null;

if (!$album) {
    http_response_code(404);
    echo 'Album nebylo nalezeno.';
    exit();
}

$photos = Db::queryAll("SELECT * FROM gallery_photos WHERE album_id = ? AND is_visible = 1 ORDER BY sort_order ASC, id ASC", $id) ?: array();
$albumImages = array();

if (!empty($album['cover_image'])) {
    $albumImages[] = array(
        'path' => $album['cover_image'],
        'title' => $album['title'],
    );
}

foreach ($photos as $photo) {
    $albumImages[] = array(
        'path' => $photo['image_path'],
        'title' => trim((string) ($photo['title'] ?? '')) !== '' ? $photo['title'] : $album['title'],
    );
}

$photoCount = app_album_display_count($album);
$pageTitle = $album['title'] . ' | Fotogalerie';
$activePage = 'fotogalerie';
$bodyClass = 'subpage-body';
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell page-hero">
            <p class="page-kicker"><?php echo app_e($album['category']); ?></p>
            <h1><?php echo app_e($album['title']); ?></h1>
            <p class="page-lead"><?php echo app_e($album['description']); ?></p>
            <p class="album-count"><?php echo $photoCount . ' ' . app_photo_count_label($photoCount); ?></p>
        </section>

        <section class="shell album-photo-grid">
            <?php foreach ($albumImages as $image): ?>
                <a
                    class="album-photo-card"
                    href="<?php echo app_e($image['path']); ?>"
                    data-lightbox-item
                    data-lightbox-src="<?php echo app_e($image['path']); ?>"
                    aria-label="Otevřít fotografii"
                >
                    <img src="<?php echo app_e($image['path']); ?>" alt="<?php echo app_e($image['title']); ?>">
                </a>
            <?php endforeach; ?>
        </section>
    </main>

    <aside class="lightbox" data-lightbox aria-hidden="true">
        <div class="lightbox-backdrop" data-lightbox-close></div>
        <div class="lightbox-dialog" role="dialog" aria-modal="true" aria-label="Náhled fotografie">
            <button class="lightbox-close" type="button" data-lightbox-close aria-label="Zavřít galerii">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 6 18 18M18 6 6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                </svg>
            </button>
            <button class="lightbox-arrow lightbox-arrow-prev" type="button" data-lightbox-prev aria-label="Předchozí fotografie">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m15 5-7 7 7 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <figure class="lightbox-figure">
                <img class="lightbox-image" data-lightbox-image src="" alt="">
            </figure>
            <button class="lightbox-arrow lightbox-arrow-next" type="button" data-lightbox-next aria-label="Další fotografie">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </aside>

<?php require_once('includes/public-footer.php'); ?>
