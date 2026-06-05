<?php
require_once('app.php');

$category = trim($_GET['category'] ?? '');
$search = trim($_GET['q'] ?? '');

$conditions = array('is_visible = 1');
$params = array();

if ($category !== '') {
    $conditions[] = 'category = ?';
    $params[] = $category;
}

if ($search !== '') {
    $conditions[] = '(title LIKE ? OR summary LIKE ? OR category LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

$whereSql = 'WHERE ' . implode(' AND ', $conditions);
$documents = Db::queryAll("SELECT * FROM documents $whereSql ORDER BY published_at DESC, id DESC", ...$params) ?: array();
$categories = Db::queryAll("SELECT DISTINCT category FROM documents WHERE is_visible = 1 ORDER BY category ASC") ?: array();

$pageTitle = 'Úřední deska | Radkovice u Budče';
$activePage = 'uredni-deska';
$bodyClass = 'subpage-body board-page';
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell page-hero page-hero-board">
            <div class="page-hero-grid">
                <div>
                    <p class="page-kicker">Oficiální dokumenty obce</p>
                    <h1>Úřední deska</h1>
                    <p class="page-lead">Oficiální dokumenty, vyhlášky a oznámení zastupitelstva obce Radkovice u Budče přehledně na jednom místě.</p>
                </div>
                <form class="page-search" method="get" action="uredni-deska.php">
                    <?php if ($category !== ''): ?>
                        <input type="hidden" name="category" value="<?php echo app_e($category); ?>">
                    <?php endif; ?>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <input type="search" name="q" value="<?php echo app_e($search); ?>" placeholder="Hledat v dokumentech...">
                </form>
            </div>

            <div class="filter-row">
                <a class="filter-chip <?php echo $category === '' ? 'active' : ''; ?>" href="uredni-deska.php<?php echo $search !== '' ? '?q=' . urlencode($search) : ''; ?>">Všechny dokumenty</a>
                <?php foreach ($categories as $item): ?>
                    <?php $cat = $item['category']; ?>
                    <a class="filter-chip <?php echo $category === $cat ? 'active' : ''; ?>" href="uredni-deska.php?category=<?php echo urlencode($cat); ?><?php echo $search !== '' ? '&q=' . urlencode($search) : ''; ?>">
                        <?php echo app_e($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="shell board-list">
            <?php foreach ($documents as $document): ?>
                <?php $boardPreview = $document['preview_image'] ?: ($document['file_path'] ?? ''); ?>
                <?php $hasPoster = $boardPreview && app_is_image_path($boardPreview); ?>
                <article class="board-entry <?php echo $hasPoster ? 'board-entry-has-poster ' : ''; ?><?php echo $document['category'] === 'Oznámení' ? 'board-entry-alert' : ''; ?>">
                    <?php if ($hasPoster): ?>
                        <a class="board-poster-link" href="document-open.php?id=<?php echo (int) $document['id']; ?>" target="_blank" rel="noreferrer" aria-label="Otevřít dokument <?php echo app_e($document['title']); ?>">
                            <img class="board-poster" src="<?php echo app_e($boardPreview); ?>" alt="<?php echo app_e($document['title']); ?>">
                        </a>
                    <?php else: ?>
                        <div class="board-icon <?php echo $document['category'] === 'Oznámení' ? 'board-icon-alert' : ''; ?>">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M8 4h6l4 4v12H8z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                <path d="M14 4v4h4M10 12h5M10 16h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="board-meta <?php echo $document['category'] === 'Oznámení' ? 'board-meta-alert' : ''; ?>">
                            <?php echo app_e($document['category']); ?> <span>•</span> Publikováno: <?php echo app_e(date('d. m. Y', strtotime($document['published_at'] ?: $document['created_at']))); ?>
                        </div>
                        <h2><?php echo app_e($document['title']); ?></h2>
                        <?php if (trim((string) $document['summary']) !== ''): ?>
                            <p><?php echo app_e($document['summary']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="board-side">
                        <div class="board-fileinfo">Velikost: <?php echo app_e($document['file_size']); ?><br>Formát: <?php echo app_e($document['file_type']); ?></div>
                        <div class="board-actions">
                            <a class="preview-button" href="document-open.php?id=<?php echo (int) $document['id']; ?>" target="_blank" rel="noreferrer">Otevřít</a>
                            <a class="download-button" href="document-download.php?id=<?php echo (int) $document['id']; ?>">Stáhnout</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
