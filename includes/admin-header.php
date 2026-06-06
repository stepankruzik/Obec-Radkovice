<?php
require_once(__DIR__ . '/../app.php');
app_require_admin();

$pageTitle = $pageTitle ?? 'Administrace';
$adminPageTitle = $adminPageTitle ?? 'Administrace';
$adminPageDescription = $adminPageDescription ?? '';
$adminActiveNav = $adminActiveNav ?? 'dashboard';
$adminActionLabel = $adminActionLabel ?? '+ Nový záznam';
$adminActionHref = $adminActionHref ?? '#editor';
$adminUser = app_admin_user();
$adminFlash = app_get_flash();
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
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo">Radkovice</div>

            <nav class="admin-nav">
                <a class="<?php echo $adminActiveNav === 'dashboard' ? 'active' : ''; ?>" href="admin.php">
                    <span class="admin-nav-icon">◘</span>
                    <span>Dashboard</span>
                </a>
                <a class="<?php echo $adminActiveNav === 'carousel' ? 'active' : ''; ?>" href="admin-carousel.php">
                    <span class="admin-nav-icon">▣</span>
                    <span>Carousel</span>
                </a>
                <a class="<?php echo $adminActiveNav === 'documents' ? 'active' : ''; ?>" href="admin-documents.php">
                    <span class="admin-nav-icon">▤</span>
                    <span>Dokumenty</span>
                </a>
                <a class="<?php echo $adminActiveNav === 'gallery' ? 'active' : ''; ?>" href="admin-gallery.php">
                    <span class="admin-nav-icon">▥</span>
                    <span>Fotogalerie</span>
                </a>
            </nav>

            <div class="admin-nav-group">Správa systému</div>

            <nav class="admin-nav admin-nav-secondary">
                <a class="<?php echo $adminActiveNav === 'users' ? 'active' : ''; ?>" href="admin-users.php">
                    <span class="admin-nav-icon">◔</span>
                    <span>Správa uživatelů</span>
                </a>
                <a class="<?php echo $adminActiveNav === 'settings' ? 'active' : ''; ?>" href="admin-settings.php">
                    <span class="admin-nav-icon">⚙</span>
                    <span>Nastavení</span>
                </a>
            </nav>

            <div class="admin-user">
                <div class="admin-avatar"><?php echo app_e(strtoupper(substr($adminUser['name'] ?? 'AD', 0, 2))); ?></div>
                <div>
                    <strong><?php echo app_e($adminUser['name'] ?? 'Administrátor'); ?></strong>
                    <span><?php echo app_e($adminUser['role'] ?? 'admin'); ?></span>
                </div>
                <a class="admin-logout" href="logout.php" aria-label="Odhlásit">↪</a>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <div>
                    <h1><?php echo app_e($adminPageTitle); ?></h1>
                    <p><?php echo app_e($adminPageDescription); ?></p>
                </div>

                <div class="admin-top-actions">
                    <form class="admin-search" method="get">
                        <span>⌕</span>
                        <input type="search" name="q" value="<?php echo app_e($_GET['q'] ?? ''); ?>" placeholder="Hledat v administraci...">
                    </form>
                    <button class="admin-theme-toggle" type="button" data-theme-toggle aria-label="Přepnout tmavý režim">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <button class="admin-bell" type="button" aria-label="Upozornění">◔</button>
                    <a class="admin-button" href="<?php echo app_e($adminActionHref); ?>">
                        <?php echo app_e($adminActionLabel); ?>
                    </a>
                </div>
            </header>

            <?php if ($adminFlash): ?>
                <div class="admin-flash admin-flash-<?php echo app_e($adminFlash['type']); ?>">
                    <?php echo app_e($adminFlash['message']); ?>
                </div>
            <?php endif; ?>
