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
        </section>

        <section class="shell album-hero-card">
            <a
                class="album-hero-image-link"
                href="<?php echo app_e($album['cover_image']); ?>"
                data-lightbox-item
                data-lightbox-src="<?php echo app_e($album['cover_image']); ?>"
            >
                <img src="<?php echo app_e($album['cover_image']); ?>" alt="<?php echo app_e($album['title']); ?>">
            </a>
        </section>

        <section class="shell album-photo-grid">
            <?php foreach ($photos as $photo): ?>
                <a
                    class="album-photo-card"
                    href="<?php echo app_e($photo['image_path']); ?>"
                    data-lightbox-item
                    data-lightbox-src="<?php echo app_e($photo['image_path']); ?>"
                    aria-label="Otevřít fotografii"
                >
                    <img src="<?php echo app_e($photo['image_path']); ?>" alt="<?php echo app_e($photo['title']); ?>">
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
