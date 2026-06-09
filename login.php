<?php
require_once('app.php');

if (!empty($_SESSION['user_id'])) {
    app_redirect('admin');
}

$errorMessage = '';

if (isset($_POST['login'])) {
    $username = app_post('username');
    $password = $_POST['password'] ?? '';

    $user = Db::queryOne("SELECT * FROM users WHERE username = ? OR name = ?", $username, $username);

    if ($user && (int) ($user['is_active'] ?? 1) === 1 && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        Db::query("UPDATE users SET last_login = ?, updated_at = ? WHERE id = ?", app_now(), app_now(), (int) $user['id']);
        app_redirect('admin');
    }

    $errorMessage = 'Neplatné uživatelské jméno nebo heslo.';
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení do správy webu</title>
    <script>
        (function () {
            var savedTheme = localStorage.getItem('site-theme');
            var theme = savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="auth-body">
    <main class="auth-layout">
        <section class="auth-visual">
            <div class="auth-overlay"></div>
            <div class="auth-brand">
                <span class="auth-kicker">Interní přístup</span>
                <h1>Správa webu obce</h1>
                <p>Vstup do administrace pro správu dokumentů, fotogalerie, uživatelů a hlavních prvků veřejného webu.</p>
                <div class="auth-points">
                    <div><strong>Obsah webu</strong><span>Správa zveřejňovaných dokumentů, alb a dalších informací.</span></div>
                    <div><strong>Homepage</strong><span>Úprava hlavních fotografií a vybraných prvků úvodní stránky.</span></div>
                    <div><strong>Interní část</strong><span>Přístup pouze pro oprávněné uživatele obce.</span></div>
                </div>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-panel-inner">
                <div class="auth-topline">
                    <a class="auth-home" href="./">← Zpět na web</a>
                    <button class="admin-theme-toggle" type="button" data-theme-toggle aria-label="Přepnout tmavý režim">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <div class="auth-card">
                    <span class="auth-badge">Správa webu</span>
                    <h2>Přihlášení do administrace</h2>
                    <p>Přihlaste se svým účtem pro interní správu obsahu a nastavení webu obce.</p>

                    <?php if ($errorMessage !== ''): ?>
                        <div class="auth-alert"><?php echo app_e($errorMessage); ?></div>
                    <?php endif; ?>

                    <form method="post" action="login" class="auth-form">
                        <label class="admin-field">
                            <span>Uživatelské jméno</span>
                            <input type="text" id="username" name="username" value="<?php echo app_e($_POST['username'] ?? ''); ?>" required>
                        </label>

                        <label class="admin-field">
                            <span>Heslo</span>
                            <input type="password" id="password" name="password" required>
                        </label>

                        <button class="auth-submit" type="submit" name="login">Přihlásit se</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <script src="assets/js/theme.js" defer></script>
</body>
</html>
