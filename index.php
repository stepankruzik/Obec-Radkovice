<?php
require_once('app.php');

$latestDocuments = Db::queryAll("SELECT * FROM documents WHERE is_visible = 1 ORDER BY published_at DESC, id DESC LIMIT 3") ?: array();
$galleryAlbums = Db::queryAll("SELECT * FROM gallery_albums WHERE is_visible = 1 ORDER BY updated_at DESC, id DESC LIMIT 2") ?: array();
$weather = app_fetch_weather();
$heroSlides = app_home_hero_slides();

$pageTitle = 'Radkovice u Budče';
$activePage = 'home';
require_once('includes/public-header.php');
?>

    <section class="hero home-hero">
        <div class="hero-carousel" data-carousel>
            <?php foreach ($heroSlides as $index => $slide): ?>
                <article
                    class="hero-slide <?php echo $index === 0 ? 'is-active' : ''; ?>"
                    data-carousel-slide
                    data-carousel-title="<?php echo app_e($slide['title']); ?>"
                    data-carousel-text="<?php echo app_e($slide['text']); ?>"
                    style="background-image: url('<?php echo app_e($slide['image']); ?>');"
                >
                    <div class="hero-slide-overlay"></div>
                    <div class="shell hero-content">
                        <div class="hero-meta desktop-only">
                            <span class="hero-badge">Kraj Vysočina</span>
                            <span>
                                <span class="hero-meta-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <rect x="4" y="5" width="16" height="15" rx="3" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <?php echo date('j. n. Y'); ?>
                            </span>
                            <span>Oficiální portál obce</span>
                        </div>

                        <h1><?php echo app_e($slide['title']); ?></h1>
                        <p class="desktop-only"><?php echo app_e($slide['text']); ?></p>

                        <form class="search-panel" action="search.php" method="get">
                            <input type="search" name="q" placeholder="Co hledáte?" required>
                            <button class="button desktop-only" type="submit">Hledat</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if (count($heroSlides) > 1): ?>
                <button class="hero-carousel-control hero-carousel-prev" type="button" data-carousel-prev aria-label="Předchozí slide">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m15 5-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="hero-carousel-control hero-carousel-next" type="button" data-carousel-next aria-label="Další slide">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="hero-carousel-dots" aria-label="Přepnutí slide">
                    <?php foreach ($heroSlides as $index => $slide): ?>
                        <button class="hero-carousel-dot <?php echo $index === 0 ? 'is-active' : ''; ?>" type="button" data-carousel-dot data-carousel-index="<?php echo (int) $index; ?>" aria-label="Slide <?php echo (int) ($index + 1); ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <main class="shell home-main">
        <section class="cards-row desktop-home" aria-label="Rychlé odkazy">
            <a class="feature-card feature-link" href="uredni-deska.php">
                <div class="icon-chip" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M8 17 4.5 7.5 8 6l3.5 9.5L8 17Zm4-1.5L8.5 6l3.5-1.5L15.5 14 12 15.5Zm4-1.5L12.5 4.5 16 3l3.5 9.5-3.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Úřední deska</h3>
                <p>Nejnovější oznámení, vyhlášky a oficiální dokumenty obce.</p>
            </a>

            <a class="feature-card feature-link" href="historie-obce.php">
                <div class="icon-chip" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M3.5 12a8.5 8.5 0 1 0 2.5-6.01L3.5 8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.5 4.5v4h4M12 7.5V12l3 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Historie obce</h3>
                <p>Stručný přehled minulosti Radkovic u Budče a proměn místního života.</p>
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
                <p>Nahlédněte do života v obci skrze fotografie z akcí i okolní krajiny.</p>
            </a>

            <a class="feature-card feature-link" href="kontakty.php">
                <div class="icon-chip" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M12 8v4l2.5 2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3>Kontakty</h3>
                <p>Spojení na starostu, zastupitelstvo, obecní úřad i úřední hodiny.</p>
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
                    <strong>Úřední deska</strong>
                </a>
                <a class="mobile-shortcut-card" href="fotogalerie.php">
                    <span class="icon-chip mobile-shortcut-accent" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.6"/>
                            <path d="m8 15 2.5-2.5L13 15l2-2 3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <strong>Fotogalerie</strong>
                </a>
            </div>

            <section class="mobile-panel mobile-panel-board">
                <div class="mobile-panel-head">
                    <h2>Historie obce</h2>
                    <a class="mobile-head-link" href="historie-obce.php">→</a>
                </div>
                <article class="mobile-news-item">
                    <span class="news-badge">Obec</span>
                    <h3>Radkovice u Budče v čase</h3>
                    <p>Od historických usedlostí přes spolkový život až po současnou podobu návsi.</p>
                    <small>Přejít na přehled historie obce</small>
                </article>
            </section>

            <section class="mobile-panel">
                <div class="mobile-panel-head">
                    <h2>Úřední deska</h2>
                    <span>📄</span>
                </div>
                <?php foreach (array_slice($latestDocuments, 0, 2) as $document): ?>
                    <a class="mobile-board-item" href="document-open.php?id=<?php echo (int) $document['id']; ?>" target="_blank" rel="noreferrer">
                        <small><?php echo app_e(date('d. m. Y', strtotime($document['published_at'] ?: $document['created_at']))); ?></small>
                        <strong><?php echo app_e($document['title']); ?></strong>
                        <span>→</span>
                    </a>
                <?php endforeach; ?>
                <a class="mobile-outline-button" href="uredni-deska.php">Zobrazit všechny dokumenty</a>
            </section>

            <section class="mobile-panel">
                <div class="mobile-panel-head">
                    <h2>Fotogalerie</h2>
                    <span>🖼</span>
                </div>
                <div class="mobile-gallery-strip">
                    <?php foreach ($galleryAlbums as $album): ?>
                        <a href="album.php?id=<?php echo (int) $album['id']; ?>">
                            <img src="<?php echo app_e($album['cover_image']); ?>" alt="<?php echo app_e($album['title']); ?>">
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="mobile-panel mobile-contact-panel">
                <div class="mobile-panel-head">
                    <h2>Kontakt</h2>
                </div>
                <div class="mobile-contact-line">
                    <strong>Obecní úřad Radkovice u Budče</strong>
                    <span>Radkovice u Budče 14<br>380 01 Dačice</span>
                </div>
                <div class="mobile-contact-line">
                    <strong>770 132 011</strong>
                </div>
                <div class="mobile-contact-line">
                    <strong>obec_radkovice@volny.cz</strong>
                </div>
                <div class="mobile-hours">
                    <strong>Úřední hodiny:</strong>
                    <div><span>Středa:</span><span>16:00 - 18:00</span></div>
                </div>
            </section>

            <section class="mobile-footer-card">
                <h2>Radkovice u Budče</h2>
                <p>Kraj Vysočina</p>
                <div class="mobile-footer-links">
                    <a href="informace-o-webu.php#povinne-informace">Povinné informace</a>
                    <a href="informace-o-webu.php#ochrana-soukromi">Ochrana soukromí</a>
                    <a href="kontakty.php">Napište nám</a>
                    <a href="informace-o-webu.php#mapa-stranek">Mapa stránek</a>
                </div>
                <small>© 2024 Obec Radkovice u Budče</small>
            </section>
        </section>

        <section class="main-grid desktop-home">
            <div>
                <div class="section-header">
                    <h2 class="section-title">Nové dokumenty</h2>
                    <a class="section-link" href="uredni-deska.php">Archiv dokumentů</a>
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
                                <a class="list-action" href="document-open.php?id=<?php echo (int) $document['id']; ?>" target="_blank" rel="noreferrer" aria-label="Otevřít dokument v nové kartě">👁</a>
                                <a class="list-action" href="document-download.php?id=<?php echo (int) $document['id']; ?>" aria-label="Stáhnout dokument">↓</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <section class="mayor-card">
                    <div class="mayor-portrait" aria-hidden="true"></div>
                    <div>
                        <h3>Slovo starosty</h3>
                        <p>"Vítejte na našich modernizovaných stránkách. Naším cílem je být vám blíž, informovat vás o všem důležitém a společně tvořit lepší místo pro život v Radkovicích."</p>
                        <div class="mayor-signature">Martin Kessner</div>
                        <div>Starosta obce Radkovice u Budče</div>
                    </div>
                </section>
            </div>

            <aside class="sidebar">
                <section class="panel weather-panel">
                    <h3>Počasí u nás</h3>
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
                                <span>pocitově <?php echo app_e((string) $weather['current']['night_temp']); ?>°C</span>
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
                                    <span>pocitově <?php echo app_e((string) $weather['tomorrow']['night_temp']); ?>°C</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p><?php echo app_e($weather['error'] ?? 'Počasí se nepodařilo načíst.'); ?></p>
                    <?php endif; ?>
                    <div class="weather-source">Zdroj: OpenWeatherMap</div>
                </section>

                <section class="panel form-panel">
                    <h3>Napište nám</h3>
                    <form>
                        <div class="field">
                            <label for="name">Jméno</label>
                            <input id="name" name="name" type="text" placeholder="Jan Novák">
                        </div>
                        <div class="field">
                            <label for="email">E-mail</label>
                            <input id="email" name="email" type="email" placeholder="vas@email.cz">
                        </div>
                        <div class="field">
                            <label for="message">Zpráva</label>
                            <textarea id="message" name="message" placeholder="Vaše zpráva pro úřad..."></textarea>
                        </div>
                        <button class="button" type="submit">Odeslat zprávu</button>
                        <div class="form-note">Odesláním souhlasíte se zpracováním osobních údajů.</div>
                    </form>
                </section>

                <section class="contact-card">
                    <h3>Kontakt</h3>
                    <p>Obecní úřad<br>Radkovice u Budče 14<br>380 01 Dačice</p>
                    <div class="contact-line">
                        <span class="contact-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M6.8 4h2.6l1.3 4-1.6 1.6a14 14 0 0 0 5 5l1.6-1.6 4 1.3v2.6a1.5 1.5 0 0 1-1.7 1.5C10.7 18 6 13.3 5.3 5.7A1.5 1.5 0 0 1 6.8 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <a href="tel:770132011">770 132 011</a>
                    </div>
                    <div class="contact-line">
                        <span class="contact-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M4 7h16v10H4z" stroke="currentColor" stroke-width="1.6"/>
                                <path d="m5 8 7 5 7-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <a href="mailto:obec_radkovice@volny.cz">obec_radkovice@volny.cz</a>
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
