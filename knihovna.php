<?php
$pageTitle = 'Knihovna | Radkovice u Budce';
$activePage = '';
$bodyClass = 'subpage-body';
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell page-hero">
            <p class="page-kicker">Obecni knihovna</p>
            <h1>Knihovna</h1>
            <p class="page-lead">Knihovna v Radkovicich vyuziva moderni knihovni program Tritius REKS. Diky nemu funguje pro nasi knihovnu i on-line katalog knih.</p>
        </section>

        <section class="shell library-section">
            <div class="library-grid">
                <article class="library-card library-card-main">
                    <h2>Sluzby knihovny</h2>
                    <p>Odkaz na on-line katalog najdete zde. Pokud nenajdete hledanou knihu v nasi obecni knihovne, knihovnice ma moznost kontaktovat jinou knihovnu z tohoto systemu a pozadovanou knihu vypujcit.</p>
                    <div class="library-actions">
                        <a class="button" href="https://trebic.tritius.cz/library/radkoviceubudce/" target="_blank" rel="noreferrer">Otevrit on-line katalog</a>
                    </div>
                </article>

                <aside class="library-card library-card-side">
                    <div class="library-meta">
                        <strong>Knihovnice</strong>
                        <span>Simona Kruzikova</span>
                    </div>
                    <div class="library-meta">
                        <strong>Vypujcni doba</strong>
                        <span>Streda 16:00 - 19:00 hod.</span>
                    </div>
                    <div class="library-meta">
                        <strong>Kontakt</strong>
                        <a href="mailto:knihovna-radkovice@seznam.cz">knihovna-radkovice@seznam.cz</a>
                    </div>
                    <div class="library-links">
                        <strong>Odkazy</strong>
                        <a href="#">Zrizovaci listina [PDF, 854 kB]</a>
                        <a href="#">Knihovni rad [PDF, 1,5 MB]</a>
                    </div>
                </aside>
            </div>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
