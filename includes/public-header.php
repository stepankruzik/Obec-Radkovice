<?php
require_once(__DIR__ . '/../app.php');

$pageTitle = $pageTitle ?? 'Radkovice u Budče';
$activePage = $activePage ?? 'home';
$bodyClass = $bodyClass ?? '';

$menuItems = array(
    array('key' => 'samosprava', 'label' => 'Samospráva', 'href' => 'samosprava'),
    array('key' => 'uredni-deska', 'label' => 'Úřední deska', 'href' => 'uredni-deska'),
    array('key' => 'historie', 'label' => 'Historie obce', 'href' => 'historie-obce'),
    array('key' => 'fotogalerie', 'label' => 'Fotogalerie', 'href' => 'fotogalerie'),
    array('key' => 'knihovna', 'label' => 'Knihovna', 'href' => 'knihovna'),
    array('key' => 'kontakty', 'label' => 'Kontakty', 'href' => 'kontakty'),
);

$mobileMenuItems = array(
    array(
        'key' => 'uredni-deska',
        'label' => 'Úřední deska',
        'href' => 'uredni-deska',
        'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 17 4.5 7.5 8 6l3.5 9.5L8 17Zm4-1.5L8.5 6l3.5-1.5L15.5 14 12 15.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M7 20h10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
    ),
    array(
        'key' => 'samosprava',
        'label' => 'Samospráva',
        'href' => 'samosprava',
        'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 10h16M6 20h12M8 10v7M12 10v7M16 10v7M12 4l8 4H4l8-4Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ),
    array(
        'key' => 'knihovna',
        'label' => 'Knihovna',
        'href' => 'knihovna',
        'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 5.5h9a2.5 2.5 0 0 1 2.5 2.5v10.5H8.5A2.5 2.5 0 0 0 6 21V5.5Zm0 0A2.5 2.5 0 0 0 3.5 8v10.5A2.5 2.5 0 0 1 6 16h11.5" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 9h5M9 12h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
    ),
    array(
        'key' => 'historie',
        'label' => 'Historie obce',
        'href' => 'historie-obce',
        'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3.5 12a8.5 8.5 0 1 0 2.5-6.01L3.5 8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.5 4.5v4h4M12 7.5V12l3 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ),
    array(
        'key' => 'fotogalerie',
        'label' => 'Fotogalerie',
        'href' => 'fotogalerie',
        'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="m8 15 2.5-2.5L13 15l2-2 3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="10" r="1.4" fill="currentColor"/></svg>',
    ),
    array(
        'key' => 'kontakty',
        'label' => 'Kontakt',
        'href' => 'kontakty',
        'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 20s6-4.35 6-10a6 6 0 1 0-12 0c0 5.65 6 10 6 10Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M12 13.2a2.2 2.2 0 1 0 0-4.4 2.2 2.2 0 0 0 0 4.4Z" stroke="currentColor" stroke-width="1.6"/></svg>',
    ),
);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo app_e($pageTitle); ?></title>
    <script>
        (function () {
            var savedTheme = localStorage.getItem('site-theme');
            var theme = savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="icon" type="image/png" href="img/znak.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?php echo app_e($bodyClass); ?>">
    <header class="topbar">
        <div class="shell topbar-inner">
            <button class="mobile-menu-button" type="button" data-mobile-menu-toggle aria-label="Otevřít navigaci" aria-expanded="false" aria-controls="mobile-menu-overlay">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <a class="brand" href="./">
                <span class="brand-mark" aria-hidden="true">
                    <img src="img/znak.png" alt="Znak obce Radkovice u Budče" width="32" height="32">
                </span>
                <span>Radkovice u Budče</span>
            </a>

            <nav class="main-nav" aria-label="Hlavní navigace">
                <?php foreach ($menuItems as $item): ?>
                    <a class="<?php echo $activePage === $item['key'] ? 'active' : ''; ?>" href="<?php echo app_e($item['href']); ?>">
                        <?php echo app_e($item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="topbar-actions">
                <button class="theme-button" type="button" data-theme-toggle aria-label="Přepnout tmavý režim">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <aside class="mobile-menu-overlay" id="mobile-menu-overlay" data-mobile-menu aria-hidden="true">
        <div class="mobile-menu-frame">
            <div class="mobile-menu-top">
                <a class="mobile-menu-brand" href="./">Radkovice u Budče</a>
                <button class="mobile-menu-close" type="button" data-mobile-menu-close aria-label="Zavřít menu">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 6 18 18M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <div class="mobile-menu-body">
                <h2 class="mobile-menu-title">Menu</h2>

                <nav class="mobile-menu-nav" aria-label="Mobilní navigace">
                    <?php foreach ($mobileMenuItems as $item): ?>
                        <a class="mobile-menu-link <?php echo $activePage === $item['key'] ? 'active' : ''; ?>" href="<?php echo app_e($item['href']); ?>">
                            <span class="mobile-menu-icon"><?php echo $item['icon']; ?></span>
                            <span><?php echo app_e($item['label']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="mobile-menu-card">
                <div class="mobile-menu-language" aria-label="Jazyk">
                    <button class="is-active" type="button">CZ</button>
                    <button type="button" disabled>EN</button>
                    <button type="button" disabled>DE</button>
                </div>

                <div class="mobile-menu-contact">
                    <div class="mobile-menu-contact-row">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 20s6-4.35 6-10a6 6 0 1 0-12 0c0 5.65 6 10 6 10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M12 13.2a2.2 2.2 0 1 0 0-4.4 2.2 2.2 0 0 0 0 4.4Z" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                        <span>Radkovice u Budče 14, 380 01 Dačice</span>
                    </div>
                    <div class="mobile-menu-contact-row">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6.8 4h2.6l1.3 4-1.6 1.6a14 14 0 0 0 5 5l1.6-1.6 4 1.3v2.6a1.5 1.5 0 0 1-1.7 1.5C10.7 18 6 13.3 5.3 5.7A1.5 1.5 0 0 1 6.8 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                        <a href="tel:770132011">+420 770 132 011</a>
                    </div>
                    <div class="mobile-menu-contact-row">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4.5 7.5A2.5 2.5 0 0 1 7 5h10a2.5 2.5 0 0 1 2.5 2.5v9A2.5 2.5 0 0 1 17 19H7a2.5 2.5 0 0 1-2.5-2.5v-9Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="m6 8 6 4 6-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <a href="mailto:obec_radkovice@volny.cz">obec_radkovice@volny.cz</a>
                    </div>
                </div>

                <div class="mobile-menu-bottom">
                    <strong>RADKOVICE</strong>
                    <div class="mobile-menu-bottom-icons" aria-hidden="true">
                        <span>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><path d="M8.5 14.5h7M9 9.5h6M12 9.5V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                        <span>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="6" cy="18" r="1.8" fill="currentColor"/><path d="M4 11a9 9 0 0 1 9 9M4 5a15 15 0 0 1 15 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </aside>
