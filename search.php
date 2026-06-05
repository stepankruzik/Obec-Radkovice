<?php
require_once('app.php');

$search = trim($_GET['q'] ?? '');
$documents = array();
$newsPosts = array();
$galleryAlbums = array();
$totalResults = 0;

if ($search !== '') {
    $like = '%' . $search . '%';

    $documents = Db::queryAll(
        "SELECT * FROM documents
         WHERE is_visible = 1
           AND (title LIKE ? OR summary LIKE ? OR category LIKE ?)
         ORDER BY published_at DESC, id DESC
         LIMIT 12",
        $like,
        $like,
        $like
    ) ?: array();

    $newsPosts = Db::queryAll(
        "SELECT * FROM news_posts
         WHERE is_visible = 1
           AND (title LIKE ? OR excerpt LIKE ? OR content LIKE ? OR category LIKE ?)
         ORDER BY published_at DESC, id DESC
         LIMIT 12",
        $like,
        $like,
        $like,
        $like
    ) ?: array();

    $galleryAlbums = Db::queryAll(
        "SELECT * FROM gallery_albums
         WHERE is_visible = 1
           AND (title LIKE ? OR description LIKE ? OR category LIKE ?)
         ORDER BY updated_at DESC, id DESC
         LIMIT 12",
        $like,
        $like,
        $like
    ) ?: array();

    $totalResults = count($documents) + count($newsPosts) + count($galleryAlbums);
}

$pageTitle = 'Vyhledávání | Radkovice u Budče';
$activePage = '';
$bodyClass = 'subpage-body';
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell page-hero">
            <div class="page-hero-grid">
                <div>
                    <p class="page-kicker">Vyhledávání na webu</p>
                    <h1>Najděte, co potřebujete</h1>
                    <p class="page-lead">Prohledávání dokumentů, aktualit a fotogalerie na jednom místě.</p>
                    <?php if ($search !== ''): ?>
                        <p class="search-results-summary">
                            Pro dotaz <strong><?php echo app_e($search); ?></strong> jsme našli <strong><?php echo $totalResults; ?></strong> výsledků.
                        </p>
                    <?php endif; ?>
                </div>

                <form class="page-search" method="get" action="search.php">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <input type="search" name="q" value="<?php echo app_e($search); ?>" placeholder="Hledat na webu..." required>
                </form>
            </div>
        </section>

        <section class="shell search-sections">
            <?php if ($search === ''): ?>
                <article class="info-box search-empty-card">
                    <h2>Zadejte hledaný výraz</h2>
                    <p>Vyhledávání umí prohledat úřední desku, aktuality i fotogalerii. Zkuste název dokumentu, téma akce nebo kategorii.</p>
                </article>
            <?php else: ?>
                <?php if ($documents): ?>
                    <section class="search-section">
                        <div class="section-header">
                            <h2 class="section-title">Dokumenty</h2>
                            <span class="search-count"><?php echo count($documents); ?> výsledků</span>
                        </div>

                        <div class="documents">
                            <?php foreach ($documents as $document): ?>
                                <article class="document-item">
                                    <div class="doc-icon" aria-hidden="true">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                            <path d="M8 4h6l4 4v12H8z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                            <path d="M14 4v4h4M10 12h6M10 16h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="document-meta">
                                            <span><?php echo app_e($document['category']); ?></span>
                                            <span class="document-tag"><?php echo app_e(date('d. m. Y', strtotime($document['published_at'] ?: $document['created_at']))); ?></span>
                                        </div>
                                        <h4><?php echo app_e($document['title']); ?></h4>
                                        <div class="document-submeta"><?php echo app_e($document['summary']); ?></div>
                                    </div>
                                    <div class="document-actions">
                                        <a class="preview-button" href="document-open.php?id=<?php echo (int) $document['id']; ?>" target="_blank" rel="noreferrer">Otevřít</a>
                                        <a class="download-button" href="document-download.php?id=<?php echo (int) $document['id']; ?>">Stáhnout</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($newsPosts): ?>
                    <section class="search-section">
                        <div class="section-header">
                            <h2 class="section-title">Aktuality</h2>
                            <span class="search-count"><?php echo count($newsPosts); ?> výsledků</span>
                        </div>

                        <div class="simple-grid">
                            <?php foreach ($newsPosts as $post): ?>
                                <article class="news-card">
                                    <div class="news-badge"><?php echo app_e($post['category']); ?></div>
                                    <h2><?php echo app_e($post['title']); ?></h2>
                                    <p><?php echo app_e($post['excerpt']); ?></p>
                                    <span>Publikováno: <?php echo app_e(date('d. m. Y', strtotime($post['published_at'] ?: $post['created_at']))); ?></span>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($galleryAlbums): ?>
                    <section class="search-section">
                        <div class="section-header">
                            <h2 class="section-title">Fotogalerie</h2>
                            <span class="search-count"><?php echo count($galleryAlbums); ?> výsledků</span>
                        </div>

                        <div class="gallery-grid">
                            <?php foreach ($galleryAlbums as $album): ?>
                                <article class="gallery-tile">
                                    <img src="<?php echo app_e($album['cover_image']); ?>" alt="<?php echo app_e($album['title']); ?>">
                                    <div class="gallery-caption">
                                        <strong><?php echo app_e($album['title']); ?></strong><br>
                                        <?php echo app_e($album['description']); ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($totalResults === 0): ?>
                    <article class="info-box search-empty-card">
                        <h2>Nic jsme nenašli</h2>
                        <p>Zkuste obecnější výraz nebo hledejte podle kategorie, například „rozpočet“, „kultura“ nebo „zastupitelstvo“.</p>
                    </article>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
