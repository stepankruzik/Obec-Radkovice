<?php
$pageTitle = 'Knihovna | Radkovice u Budče';
$activePage = 'knihovna';
$bodyClass = 'subpage-body';
$libraryDocuments = array(
    array(
        'label' => 'Zřizovací listina [PDF, 854 kB]',
        'href' => 'uploads/library/zrizovaci-listina.pdf',
    ),
    array(
        'label' => 'Knihovní řád [PDF, 1,5 MB]',
        'href' => 'uploads/library/knihovni-rad.pdf',
    ),
);
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell page-hero">
            <p class="page-kicker">Obecní knihovna</p>
            <h1>Knihovna</h1>
            <p class="page-lead">Knihovna v Radkovicích využívá moderní knihovní program Tritius REKS. Díky němu funguje pro naši knihovnu i on-line katalog knih.</p>
        </section>

        <section class="shell library-section">
            <div class="library-grid">
                <article class="library-card library-card-main">
                    <span class="library-eyebrow">Služby a katalog</span>
                    <h2>Služby knihovny</h2>
                    <p>Odkaz na on-line katalog najdete zde. Pokud nenajdete hledanou knihu v naší obecní knihovně, knihovnice má možnost kontaktovat jinou knihovnu z tohoto systému a požadovanou knihu vypůjčit.</p>
                    <div class="library-actions">
                        <a class="button" href="https://trebic.tritius.cz/library/radkoviceubudce/" target="_blank" rel="noreferrer">Otevřít on-line katalog</a>
                    </div>
                </article>

                <aside class="library-card library-card-side">
                    <div class="library-meta">
                        <strong>Knihovnice</strong>
                        <span>Simona Kružíková</span>
                    </div>
                    <div class="library-meta">
                        <strong>Výpůjční doba</strong>
                        <span>Středa 16:00 - 19:00 hod.</span>
                    </div>
                    <div class="library-meta">
                        <strong>Kontakt</strong>
                        <a href="mailto:knihovna-radkovice@seznam.cz">knihovna-radkovice@seznam.cz</a>
                    </div>
                    <div class="library-links">
                        <strong>Odkazy</strong>
                        <?php foreach ($libraryDocuments as $document): ?>
                            <a href="<?php echo app_e($document['href']); ?>" target="_blank" rel="noreferrer"><?php echo app_e($document['label']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </aside>
            </div>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
