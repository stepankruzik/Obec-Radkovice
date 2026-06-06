<?php
require_once('app.php');

$albums = Db::queryAll("SELECT * FROM gallery_albums WHERE is_visible = 1 ORDER BY event_date DESC, updated_at DESC, id DESC") ?: array();

$pageTitle = 'Fotogalerie | Radkovice u Budče';
$activePage = 'fotogalerie';
$bodyClass = 'subpage-body';
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell page-hero">
            <p class="page-kicker">Obec ve fotografiích</p>
            <h1>Fotogalerie</h1>
            <p class="page-lead">Projděte si jednotlivá alba z akcí, proměn návsi i pohledů do okolní krajiny.</p>
        </section>

        <section class="shell gallery-grid">
            <?php foreach ($albums as $index => $album): ?>
                <?php $photoCount = app_album_display_count($album); ?>
                <a class="gallery-tile gallery-link <?php echo $index === 0 ? 'gallery-tile-large' : ''; ?>" href="album.php?id=<?php echo (int) $album['id']; ?>">
                    <img src="<?php echo app_e($album['cover_image']); ?>" alt="<?php echo app_e($album['title']); ?>">
                    <div class="gallery-caption">
                        <strong><?php echo app_e($album['title']); ?></strong><br>
                        <?php echo app_e($album['description']); ?><br>
                        <span><?php echo app_e(date('d. m. Y', strtotime((string) $album['event_date']))); ?> • <?php echo $photoCount . ' ' . app_photo_count_label($photoCount); ?> • <?php echo app_e($album['category']); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
