<?php
http_response_code(404);
$pageTitle = '404 | Stránka nenalezena';
$bodyClass = 'subpage-body';
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell error-hero">
            <div class="error-card">
                <span class="page-kicker">Chyba 404</span>
                <h1>Tahle stránka se asi odstěhovala bez ohlášení.</h1>
                <p class="page-lead">Zkusili jsme ji najít v kronice, na úřední desce i ve fotogalerii, ale nikde nebyla. Možná je adresa špatně, nebo už stránka neexistuje.</p>
                <div class="error-actions">
                    <a class="button" href="./">Zpět na úvod</a>
                    <a class="button button-secondary" href="uredni-deska">Úřední deska</a>
                </div>
            </div>

            <div class="error-side-card">
                <strong>Co můžete zkusit</strong>
                <a href="samosprava">Samospráva</a>
                <a href="fotogalerie">Fotogalerie</a>
                <a href="knihovna">Knihovna</a>
                <a href="kontakty">Kontakty</a>
            </div>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
