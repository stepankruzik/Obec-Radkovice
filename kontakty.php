<?php
$pageTitle = 'Kontakty | Radkovice u Budče';
$activePage = 'kontakty';
$bodyClass = 'subpage-body';
require_once('includes/public-header.php');
?>

    <main class="subpage-main">
        <section class="shell page-hero">
            <p class="page-kicker">Spojte se s námi</p>
            <h1>Kontakty</h1>
            <p class="page-lead">Oficiální kontaktní údaje obce, úřední hodiny a základní identifikační informace přehledně na jednom místě.</p>
        </section>

        <section class="shell shell-wide contacts-layout">
            <article class="contact-detail-card contact-detail-card-brand">
                <div class="contact-card-head">
                    <img class="contact-crest" src="img/znak.png" alt="Znak obce Radkovice u Budče">
                    <div>
                        <h2>Obecní úřad Radkovice u Budče</h2>
                        <p>Obecní úřad<br>Radkovice u Budče 14<br>380 01 Dačice</p>
                    </div>
                </div>

                <div class="contact-detail-line"><strong>Telefon:</strong> <a href="tel:770132011">770 132 011</a></div>
                <div class="contact-detail-line"><strong>Starosta:</strong> Martin Kessner</div>
                <div class="contact-detail-line"><strong>E-mail:</strong> <a href="mailto:obec_radkovice@volny.cz">obec_radkovice@volny.cz</a></div>
                <div class="contact-detail-line"><strong>WWW:</strong> <a href="http://www.radkoviceubudce.cz/" target="_blank" rel="noreferrer">www.radkoviceubudce.cz</a></div>
                <div class="contact-detail-line"><strong>ID datové schránky:</strong> 9pfj2mc</div>
                <div class="contact-detail-line"><strong>DIČ:</strong> CZ00378534</div>
                <div class="contact-detail-line"><strong>Bankovní spojení:</strong> 19820711 / 0100</div>
                <div class="contact-detail-line"><strong>Úřední hodiny:</strong> <span>Středa 16:00 - 18:00</span></div>
            </article>

            <article class="office-hours-card office-hours-map-card">
                <h2>Mapa</h2>

                <div class="contact-map-frame">
                    <iframe
                        title="Google mapa Radkovice u Budče"
                        src="https://www.google.com/maps?q=Radkovice%20u%20Bud%C4%8De%2014%2C%20380%2001%20Da%C4%8Dice&z=15&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </article>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
