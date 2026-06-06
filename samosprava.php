<?php
$pageTitle = 'Samospráva | Radkovice u Budče';
$activePage = 'samosprava';
$bodyClass = 'subpage-body';
$councilMembers = array(
    'Antonín Blažek',
    'Jan Toman',
    'Petra Havlíčková',
    'Vladislav Kopeček',
    'Vladimír Kněžiček',
);
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell page-hero page-hero-governance">
            <p class="page-kicker">Vedení obce</p>
            <h1>Samospráva</h1>
            <p class="page-lead">Přehled vedení obce Radkovice u Budče, kontaktů na zástupce samosprávy a aktuálního složení zastupitelstva na jednom místě.</p>
        </section>

        <section class="shell governance-layout">
            <article class="governance-card governance-card-featured">
                <div class="governance-card-kicker">Starosta obce</div>
                <h2>Martin Kessner</h2>
                <p>Hlavní kontakt pro chod obce, komunikaci s občany a koordinaci obecních záležitostí.</p>
                <div class="governance-contact-list">
                    <div class="governance-contact-item">
                        <strong>Telefon</strong>
                        <a href="tel:770132011">770 132 011</a>
                    </div>
                    <div class="governance-contact-item">
                        <strong>E-mail</strong>
                        <a href="mailto:obec_radkovice@volny.cz">obec_radkovice@volny.cz</a>
                    </div>
                </div>
            </article>

            <article class="governance-card governance-card-accent">
                <div class="governance-card-kicker">Zástupce starosty</div>
                <h2>Zdeněk Kružík</h2>
                <p>Podílí se na správě obce, zastupuje starostu a pomáhá s běžnou agendou samosprávy.</p>
            </article>
        </section>

        <section class="shell governance-grid governance-grid-extended">
            <article class="governance-card governance-card-council">
                <div class="governance-card-kicker">Zastupitelstvo</div>
                <h2>Členové zastupitelstva</h2>
                <div class="governance-member-list">
                    <?php foreach ($councilMembers as $member): ?>
                        <div class="governance-member-item">
                            <span class="governance-member-dot" aria-hidden="true"></span>
                            <span><?php echo app_e($member); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>

            <article class="governance-card governance-card-info">
                <div class="governance-card-kicker">Úřední informace</div>
                <h2>Kontakt s obcí</h2>
                <p>Osobní jednání i běžné dotazy je možné řešit přes obecní úřad. Pro oficiální dokumenty a oznámení sledujte také úřední desku.</p>
                <div class="governance-actions">
                    <a class="preview-button" href="kontakty.php">Zobrazit kontakty</a>
                    <a class="download-button" href="uredni-deska.php">Úřední deska</a>
                </div>
            </article>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
