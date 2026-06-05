<?php
require_once('app.php');

$latestDocuments = Db::queryAll("SELECT * FROM documents WHERE is_visible = 1 ORDER BY published_at DESC, id DESC LIMIT 3") ?: array();
$galleryAlbums = Db::queryAll("SELECT * FROM gallery_albums WHERE is_visible = 1 ORDER BY updated_at DESC, id DESC LIMIT 2") ?: array();
$weather = app_fetch_weather();
$homeSlides = app_home_slides();

$pageTitle = 'Radkovice u Budce';
$activePage = 'home';
require_once('includes/public-header.php');
?>

    <section class="hero home-hero home-carousel" data-carousel>
        <div class="home-carousel-track" data-carousel-track>
            <?php foreach ($homeSlides as $index => $slide): ?>
                <article
                    class="home-slide <?php echo $index === 0 ? 'is-active' : ''; ?>"
                    data-carousel-slide
                    style="background-image: linear-gradient(180deg, rgba(9, 20, 11, 0.12), rgba(9, 20, 11, 0.42)), url('<?php echo app_e($slide['image']); ?>');"
                >
                    <div class="shell hero-content">
                        <div class="hero-meta desktop-only">
                            <span class="hero-badge"><?php echo app_e($slide['badge']); ?></span>
                            <span>
                                <span class="hero-meta-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <rect x="4" y="5" width="16" height="15" rx="3" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <?php echo date('j. n. Y'); ?>
                            </span>
                            <span>Oficialni portal obce</span>
                        </div>

                        <h1><?php echo app_e($slide['title']); ?></h1>
                        <p><?php echo app_e($slide['text']); ?></p>

                        <form class="search-panel desktop-only" action="search.php" method="get">
                            <input type="search" name="q" placeholder="Co hledate?" required>
                            <button class="button" type="submit">Hledat</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if (count($homeSlides) > 1): ?>
            <div class="home-carousel-controls shell">
                <button class="home-carousel-arrow" type="button" data-carousel-prev aria-label="Predchozi snimek">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M15 5 8 12l7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="home-carousel-dots" aria-label="Vyber snimku">
                    <?php foreach ($homeSlides as $index => $slide): ?>
                        <button
                            class="home-carousel-dot <?php echo $index === 0 ? 'is-active' : ''; ?>"
                            type="button"
                            data-carousel-dot
                            data-carousel-index="<?php echo $index; ?>"
                            aria-label="Zobrazit snimek <?php echo $index + 1; ?>"
                        ></button>
                    <?php endforeach; ?>
                </div>
                <button class="home-carousel-arrow" type="button" data-carousel-next aria-label="Dalsi snimek">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </section>

    <main class="shell home-main">
        <section class="cards-row desktop-home" aria-label="Rychle odkazy">
            <a class="feature-card feature-link" href="uredni-deska.php">
                <div class="icon-chip" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M8 17 4.5 7.5 8 6l3.5 9.5L8 17Zm4-1.5L8.5 6l3.5-1.5L15.5 14 12 15.5Zm4-1.5L12.5 4.5 16 3l3.5 9.5-3.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Uredni deska</h3>
                <p>Nejnovejsi oznameni, vyhlasky a oficialni dokumenty obce.</p>
            </a>

            <a class="feature-card feature-link" href="samosprava.php">
                <div class="icon-chip" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M4 10h16M6 20h12M8 10v7M12 10v7M16 10v7M12 4l8 4H4l8-4Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Samosprava</h3>
                <p>Starosta, zastupitelstvo, komise i obecni knihovna na jednom miste.</p>
            </a>

            <a class="feature-card feature-link" href="fotogalerie.php">
                <div class="icon-chip" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.6"/>
                        <path d="m8 15 2.5-2.5L13 15l2-2 3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="10" r="1.4" fill="currentColor"/>
                    </svg>
                </div>
                <h3>Fotogalerie</h3>
                <p>Nahlednete do zivota v obci skrze fotografie z akci i okoli.</p>
            </a>

            <a class="feature-card feature-link" href="kontakty.php">
                <div class="icon-chip" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M6.8 4h2.6l1.3 4-1.6 1.6a14 14 0 0 0 5 5l1.6-1.6 4 1.3v2.6a1.5 1.5 0 0 1-1.7 1.5C10.7 18 6 13.3 5.3 5.7A1.5 1.5 0 0 1 6.8 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Kontakty</h3>
                <p>Spojeni na obecni urad, starostu a uredni hodiny.</p>
            </a>
        </section>

        <section class="mobile-home">
            <div class="mobile-shortcuts">
                <a class="mobile-shortcut-card" href="uredni-deska.php">
                    <span class="icon-chip" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M8 17 4.5 7.5 8 6l3.5 9.5L8 17Zm4-1.5L8.5 6l3.5-1.5L15.5 14 12 15.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <strong>Uredni deska</strong>
                    <p>Dulezite vyhlasky a dokumenty</p>
                </a>
                <a class="mobile-shortcut-card" href="samosprava.php">
                    <span class="icon-chip mobile-shortcut-accent" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M4 10h16M6 20h12M8 10v7M12 10v7M16 10v7M12 4l8 4H4l8-4Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <strong>Samosprava</strong>
                    <p>Vedeni obce a knihovna</p>
                </a>
            </div>

            <section class="mobile-panel">
                <div class="mobile-panel-head">
                    <h2>Nove dokumenty</h2>
                    <a class="mobile-head-link" href="uredni-deska.php">Zobrazit vse</a>
                </div>
                <?php foreach (array_slice($latestDocuments, 0, 2) as $document): ?>
                    <a class="mobile-board-item" href="document-download.php?id=<?php echo (int) $document['id']; ?>">
                        <small><?php echo app_e(date('d. m. Y', strtotime($document['published_at'] ?: $document['created_at']))); ?></small>
                        <strong><?php echo app_e($document['title']); ?></strong>
                        <span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 4v10M8.5 10.5 12 14l3.5-3.5M5 18h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </a>
                <?php endforeach; ?>
            </section>

            <section class="mobile-panel">
                <div class="mobile-panel-head">
                    <h2>Fotogalerie</h2>
                    <a class="mobile-head-link" href="fotogalerie.php">Vice foto</a>
                </div>
                <div class="mobile-gallery-strip">
                    <?php foreach ($galleryAlbums as $album): ?>
                        <a href="album.php?id=<?php echo (int) $album['id']; ?>">
                            <img src="<?php echo app_e($album['cover_image']); ?>" alt="<?php echo app_e($album['title']); ?>">
                        </a>
                    <?php endforeach; ?>
                    <a class="mobile-gallery-more" href="fotogalerie.php">+12</a>
                </div>
            </section>

            <section class="mobile-footer-card">
                <h2>Obec Radkovice</h2>
                <p>Radkovice u Budce 14, 675 32 Budkov</p>
                <small>ID datove schranky: 6p5ay8y</small>
                <div class="mobile-footer-links">
                    <a href="#">Povinne informace</a>
                    <a href="#">Ochrana soukromi</a>
                    <a href="#">Pristupnost</a>
                </div>
                <strong class="mobile-footer-copy">© 2024 Obec Radkovice u Budce</strong>
            </section>
        </section>

        <section class="main-grid desktop-home">
            <div>
                <div class="section-header">
                    <h2 class="section-title">Nove dokumenty</h2>
                    <a class="section-link" href="uredni-deska.php">Archiv dokumentu</a>
                </div>

                <div class="documents">
                    <?php foreach ($latestDocuments as $document): ?>
                        <article class="document-item">
                            <div class="doc-icon" aria-hidden="true">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                    <path d="M8 4h6l4 4v12H8z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                    <path d="M14 4v4h4M10 12h6M10 16h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <div class="document-meta">
                                    <span><?php echo app_e(date('j. n. Y', strtotime($document['published_at'] ?: $document['created_at']))); ?></span>
                                    <span class="document-tag"><?php echo app_e($document['category']); ?></span>
                                </div>
                                <h4><?php echo app_e($document['title']); ?></h4>
                                <div class="document-submeta">
                                    <?php echo trim((string) $document['summary']) !== '' ? app_e($document['summary']) . ' • ' : ''; ?>
                                    <?php echo app_e($document['file_type']); ?>, <?php echo app_e($document['file_size']); ?>
                                </div>
                            </div>
                            <div class="document-actions">
                                <a class="list-action" href="document-open.php?id=<?php echo (int) $document['id']; ?>" target="_blank" rel="noreferrer" aria-label="Otevrit dokument v nove karte">👁</a>
                                <a class="list-action" href="document-download.php?id=<?php echo (int) $document['id']; ?>" aria-label="Stahnout dokument">↓</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <section class="mayor-card">
                    <div class="mayor-portrait" aria-hidden="true"></div>
                    <div>
                        <h3>Slovo starosty</h3>
                        <p>"Vitejte na nasich modernizovanych strankach. Nasim cilem je byt vam bliz, informovat vas o vsem dulezitem a spolecne tvorit lepsi misto pro zivot v Radkovicich."</p>
                        <div class="mayor-signature">Martin Kessner</div>
                        <div>Starosta obce Radkovice u Budce</div>
                    </div>
                </section>
            </div>

            <aside class="sidebar">
                <section class="panel weather-panel">
                    <h3>Pocasi u nas</h3>
                    <?php if ($weather['ok'] && !empty($weather['current'])): ?>
                        <div class="weather-row">
                            <div class="weather-icon <?php echo ($weather['current']['icon'] ?? '') === 'rain' ? 'weather-icon-rain' : ''; ?>" aria-hidden="true">
                                <?php if (($weather['current']['icon'] ?? '') === 'rain'): ?>
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 18h9a4 4 0 0 0 .7-7.94A5.5 5.5 0 0 0 6.04 8.74 4.5 4.5 0 0 0 7 18Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                        <path d="M9 19.5 7.8 21M13 19.5 11.8 21M17 19.5 15.8 21" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                <?php else: ?>
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/>
                                        <path d="M12 2.5v3M12 18.5v3M21.5 12h-3M5.5 12h-3M18.7 5.3l-2.1 2.1M7.4 16.6l-2.1 2.1M18.7 18.7l-2.1-2.1M7.4 7.4 5.3 5.3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <strong><?php echo app_e($weather['current']['label']); ?></strong>
                                <p><?php echo app_e($weather['current']['description']); ?></p>
                            </div>
                            <div class="weather-temp">
                                <strong><?php echo app_e((string) $weather['current']['temp']); ?>°C</strong>
                                <span>pocitove <?php echo app_e((string) $weather['current']['night_temp']); ?>°C</span>
                            </div>
                        </div>

                        <?php if (!empty($weather['tomorrow'])): ?>
                            <div class="weather-row">
                                <div class="weather-icon <?php echo ($weather['tomorrow']['icon'] ?? '') === 'rain' ? 'weather-icon-rain' : ''; ?>" aria-hidden="true">
                                    <?php if (($weather['tomorrow']['icon'] ?? '') === 'rain'): ?>
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                            <path d="M7 18h9a4 4 0 0 0 .7-7.94A5.5 5.5 0 0 0 6.04 8.74 4.5 4.5 0 0 0 7 18Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                            <path d="M9 19.5 7.8 21M13 19.5 11.8 21M17 19.5 15.8 21" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    <?php else: ?>
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/>
                                            <path d="M12 2.5v3M12 18.5v3M21.5 12h-3M5.5 12h-3M18.7 5.3l-2.1 2.1M7.4 16.6l-2.1 2.1M18.7 18.7l-2.1-2.1M7.4 7.4 5.3 5.3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong><?php echo app_e($weather['tomorrow']['label']); ?></strong>
                                    <p><?php echo app_e($weather['tomorrow']['description']); ?></p>
                                </div>
                                <div class="weather-temp">
                                    <strong><?php echo app_e((string) $weather['tomorrow']['temp']); ?>°C</strong>
                                    <span>noc <?php echo app_e((string) $weather['tomorrow']['night_temp']); ?>°C</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Predpoved se nepodarilo nacist. Zkuste to prosim pozdeji.</p>
                    <?php endif; ?>
                    <div class="weather-source">Zdroj: OpenWeatherMap</div>
                </section>

                <section class="contact-card">
                    <h3>Kontakt</h3>
                    <p><?php echo nl2br(app_e(app_setting('office_address', "Obecni urad\nRadkovice u Budce 14\n380 01 Dacice"))); ?></p>
                    <div class="contact-line">
                        <span class="contact-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M6.8 4h2.6l1.3 4-1.6 1.6a14 14 0 0 0 5 5l1.6-1.6 4 1.3v2.6a1.5 1.5 0 0 1-1.7 1.5C10.7 18 6 13.3 5.3 5.7A1.5 1.5 0 0 1 6.8 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <a href="tel:<?php echo preg_replace('/\s+/', '', app_setting('contact_phone', '770 132 011')); ?>"><?php echo app_e(app_setting('contact_phone', '770 132 011')); ?></a>
                    </div>
                    <div class="contact-line">
                        <span class="contact-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M4.5 7.5A2.5 2.5 0 0 1 7 5h10a2.5 2.5 0 0 1 2.5 2.5v9A2.5 2.5 0 0 1 17 19H7a2.5 2.5 0 0 1-2.5-2.5v-9Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="m6 8 6 4 6-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <a href="mailto:<?php echo app_e(app_setting('contact_email', 'obec_radkovice@volny.cz')); ?>"><?php echo app_e(app_setting('contact_email', 'obec_radkovice@volny.cz')); ?></a>
                    </div>
                    <div class="contact-buttons">
                        <a class="map-button" href="kontakty.php">Mapa</a>
                        <a class="map-button primary" href="kontakty.php">Detail</a>
                    </div>
                </section>
            </aside>
        </section>
    </main>

<?php require_once('includes/public-footer.php'); ?>
