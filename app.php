<?php
require_once('db.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Db::connect('localhost', 'radkovice', 'root', '');

Db::query("CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(120) NOT NULL,
    setting_value TEXT NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

Db::query("ALTER TABLE documents
    ADD COLUMN IF NOT EXISTS file_path VARCHAR(255) NULL AFTER summary,
    ADD COLUMN IF NOT EXISTS original_name VARCHAR(255) NULL AFTER file_path,
    ADD COLUMN IF NOT EXISTS file_mime VARCHAR(120) NULL AFTER original_name,
    ADD COLUMN IF NOT EXISTS preview_image VARCHAR(255) NULL AFTER file_mime");

Db::query("CREATE TABLE IF NOT EXISTS gallery_photos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    album_id INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL DEFAULT '',
    image_path VARCHAR(255) NOT NULL,
    sort_order INT(11) NOT NULL DEFAULT 0,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

Db::query("ALTER TABLE gallery_albums
    ADD COLUMN IF NOT EXISTS event_date DATE NULL AFTER description");

Db::query("UPDATE gallery_albums
    SET event_date = DATE(created_at)
    WHERE event_date IS NULL AND created_at IS NOT NULL");

function app_redirect(string $url): void
{
    header("Location: $url");
    exit();
}

function app_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function app_require_admin(): void
{
    if (empty($_SESSION['user_id'])) {
        app_redirect('login.php');
    }
}

function app_set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = array(
        'type' => $type,
        'message' => $message,
    );
}

function app_get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function app_post(string $key, string $default = ''): string
{
    return trim($_POST[$key] ?? $default);
}

function app_now(): string
{
    return date('Y-m-d H:i:s');
}

function app_fetch_all(string $table, string $orderBy = 'id DESC'): array
{
    return Db::queryAll("SELECT * FROM `$table` ORDER BY $orderBy") ?: array();
}

function app_find_by_id(string $table, int $id): ?array
{
    $row = Db::queryOne("SELECT * FROM `$table` WHERE id = ?", $id);
    return $row ?: null;
}

function app_delete_by_id(string $table, int $id): void
{
    Db::query("DELETE FROM `$table` WHERE id = ?", $id);
}

function app_toggle_visibility(string $table, int $id): void
{
    $row = app_find_by_id($table, $id);
    if (!$row || !array_key_exists('is_visible', $row)) {
        return;
    }

    Db::update($table, array('is_visible' => $row['is_visible'] ? 0 : 1, 'updated_at' => app_now()), 'WHERE id = ?', $id);
}

function app_setting(string $key, string $default = ''): string
{
    $value = Db::querySingle("SELECT setting_value FROM site_settings WHERE setting_key = ?", $key);
    return $value !== false && $value !== null ? (string) $value : $default;
}

function app_save_setting(string $key, string $value): void
{
    $exists = Db::queryOne("SELECT setting_key FROM site_settings WHERE setting_key = ?", $key);

    if ($exists) {
        Db::query(
            "UPDATE site_settings SET setting_value = ?, updated_at = ? WHERE setting_key = ?",
            $value,
            app_now(),
            $key
        );
        return;
    }

    Db::insert('site_settings', array(
        'setting_key' => $key,
        'setting_value' => $value,
        'updated_at' => app_now(),
    ));
}

function app_home_slides(): array
{
    $slides = array();

    for ($index = 1; $index <= 4; $index++) {
        $defaultImage = $index === 1 ? 'img/uvod.JPG' : '';
        $defaultTitle = $index === 1 ? 'Vitejte v Radkovicich u Budce' : '';
        $defaultText = $index === 1 ? 'Oficialni informacni portal obce. Dokumenty, fotogalerie, kontakty i informace o obci na jednom miste.' : '';
        $defaultBadge = $index === 1 ? 'Vitejte u nas' : 'Obecni zivot';

        $image = app_setting("home_slide_{$index}_image", $defaultImage);
        $title = app_setting("home_slide_{$index}_title", $defaultTitle);
        $text = app_setting("home_slide_{$index}_text", $defaultText);
        $badge = app_setting("home_slide_{$index}_badge", $defaultBadge);

        if ($image === '' && $title === '' && $text === '') {
            continue;
        }

        $slides[] = array(
            'image' => $image !== '' ? $image : 'img/uvod.JPG',
            'title' => $title !== '' ? $title : 'Radkovice u Budce',
            'text' => $text !== '' ? $text : 'Oficialni informacni portal obce.',
            'badge' => $badge !== '' ? $badge : 'Vitejte u nas',
        );
    }

    if (!$slides) {
        $slides[] = array(
            'image' => 'img/uvod.JPG',
            'title' => 'Vitejte v Radkovicich u Budce',
            'text' => 'Oficialni informacni portal obce. Dokumenty, fotogalerie, kontakty i informace o obci na jednom miste.',
            'badge' => 'Vitejte u nas',
        );
    }

    return $slides;
}

function app_admin_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $user = Db::queryOne("SELECT * FROM users WHERE id = ?", (int) $_SESSION['user_id']);
    return $user ?: null;
}

function app_public_path(string $relativePath): string
{
    return __DIR__ . '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
}

function app_ensure_directory(string $relativePath): string
{
    $path = app_public_path($relativePath);
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    return $path;
}

function app_upload_file(array $file, string $directory, array $allowedExtensions = array()): ?array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Soubor se nepodařilo nahrát.');
    }

    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if ($allowedExtensions && !in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('Nepodporovaný typ souboru.');
    }

    $safeBaseName = preg_replace('/[^a-zA-Z0-9_-]+/', '-', pathinfo($file['name'] ?? 'soubor', PATHINFO_FILENAME));
    $safeBaseName = trim((string) $safeBaseName, '-');
    if ($safeBaseName === '') {
        $safeBaseName = 'soubor';
    }

    $relativeDirectory = trim(str_replace('\\', '/', $directory), '/');
    $targetDirectory = app_ensure_directory($relativeDirectory);
    $targetName = $safeBaseName . '-' . uniqid('', true) . ($extension !== '' ? '.' . $extension : '');
    $targetRelativePath = $relativeDirectory . '/' . $targetName;
    $targetAbsolutePath = app_public_path($targetRelativePath);

    if (!move_uploaded_file($file['tmp_name'], $targetAbsolutePath)) {
        throw new RuntimeException('Nahraný soubor se nepodařilo uložit.');
    }

    return array(
        'path' => $targetRelativePath,
        'name' => $file['name'] ?? $targetName,
        'mime' => mime_content_type($targetAbsolutePath) ?: 'application/octet-stream',
        'size' => app_human_filesize((int) filesize($targetAbsolutePath)),
        'extension' => strtoupper($extension ?: pathinfo($targetName, PATHINFO_EXTENSION)),
    );
}

function app_delete_uploaded_file(?string $relativePath): void
{
    if (!$relativePath) {
        return;
    }

    $absolutePath = app_public_path($relativePath);
    if (is_file($absolutePath)) {
        unlink($absolutePath);
    }
}

function app_human_filesize(int $bytes): string
{
    if ($bytes <= 0) {
        return '0 B';
    }

    $units = array('B', 'KB', 'MB', 'GB');
    $power = (int) floor(log($bytes, 1024));
    $power = min($power, count($units) - 1);
    $value = $bytes / (1024 ** $power);

    return number_format($value, $power === 0 ? 0 : 1, '.', ' ') . ' ' . $units[$power];
}

function app_is_image_path(?string $path): bool
{
    if (!$path) {
        return false;
    }

    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return in_array($extension, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'), true);
}

function app_document_status_label(string $status): string
{
    $labels = array(
        'draft' => 'Koncept',
        'review' => 'Ke kontrole',
        'published' => 'Zveřejněno',
    );

    return $labels[$status] ?? $status;
}

function app_gallery_status_label(string $status): string
{
    $labels = array(
        'public' => 'Veřejné',
        'draft' => 'Koncept',
    );

    return $labels[$status] ?? $status;
}

function app_refresh_album_count(int $albumId): void
{
    $count = (int) (Db::querySingle("SELECT COUNT(*) FROM gallery_photos WHERE album_id = ? AND is_visible = 1", $albumId) ?? 0);
    Db::update('gallery_albums', array('item_count' => $count, 'updated_at' => app_now()), 'WHERE id = ?', $albumId);
}

function app_http_get_json(string $url): ?array
{
    $context = stream_context_create(array(
        'http' => array(
            'method' => 'GET',
            'timeout' => 10,
            'ignore_errors' => true,
            'header' => "User-Agent: Radkovice2/1.0\r\n",
        ),
    ));

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return null;
    }

    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : null;
}

function app_weather_icon_name(?string $iconCode, ?string $description = null): string
{
    $iconCode = (string) $iconCode;
    $description = mb_strtolower((string) $description, 'UTF-8');

    if (str_contains($iconCode, '09') || str_contains($iconCode, '10') || str_contains($iconCode, '11') || str_contains($description, 'déšť') || str_contains($description, 'přehá')) {
        return 'rain';
    }

    return 'sun';
}

function app_pick_tomorrow_forecast(array $forecastItems): ?array
{
    if (!$forecastItems) {
        return null;
    }

    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $bestItem = null;
    $bestDiff = PHP_INT_MAX;

    foreach ($forecastItems as $item) {
        $dateText = (string) ($item['dt_txt'] ?? '');
        if ($dateText === '' || !str_starts_with($dateText, $tomorrow)) {
            continue;
        }

        $hour = (int) date('G', strtotime($dateText));
        $diff = abs($hour - 12);
        if ($diff < $bestDiff) {
            $bestDiff = $diff;
            $bestItem = $item;
        }
    }

    if ($bestItem) {
        return $bestItem;
    }

    foreach ($forecastItems as $item) {
        $dateText = (string) ($item['dt_txt'] ?? '');
        if ($dateText !== '' && str_starts_with($dateText, $tomorrow)) {
            return $item;
        }
    }

    return null;
}

function app_fetch_weather(): array
{
    $apiKey = trim(app_setting('weather_api_key', ''));
    $lat = trim(app_setting('weather_lat', '49.087222'));
    $lon = trim(app_setting('weather_lon', '15.634444'));
    $cachePath = app_public_path('cache/weather.json');
    $cacheTtl = 1800;

    $fallback = array(
        'ok' => false,
        'current' => null,
        'tomorrow' => null,
        'source' => 'OpenWeatherMap',
        'error' => 'Počasí zatím není nastavené.',
    );

    if ($apiKey === '') {
        return $fallback;
    }

    if (is_file($cachePath) && (time() - filemtime($cachePath) < $cacheTtl)) {
        $cached = json_decode((string) file_get_contents($cachePath), true);
        if (is_array($cached)) {
            return $cached;
        }
    }

    $query = http_build_query(array(
        'lat' => $lat,
        'lon' => $lon,
        'appid' => $apiKey,
        'units' => 'metric',
        'lang' => 'cz',
    ));

    $currentData = app_http_get_json('https://api.openweathermap.org/data/2.5/weather?' . $query);
    $forecastData = app_http_get_json('https://api.openweathermap.org/data/2.5/forecast?' . $query);

    if (!$currentData || !$forecastData || (int) ($currentData['cod'] ?? 200) !== 200) {
        return array_merge($fallback, array('error' => 'Nepodařilo se načíst živé počasí.'));
    }

    $tomorrowItem = app_pick_tomorrow_forecast($forecastData['list'] ?? array());
    $currentWeather = $currentData['weather'][0] ?? array();
    $tomorrowWeather = $tomorrowItem['weather'][0] ?? array();

    $payload = array(
        'ok' => true,
        'source' => 'OpenWeatherMap',
        'error' => null,
        'current' => array(
            'label' => 'Dnes',
            'description' => ucfirst((string) ($currentWeather['description'] ?? '')),
            'temp' => (int) round((float) ($currentData['main']['temp'] ?? 0)),
            'night_temp' => (int) round((float) ($currentData['main']['feels_like'] ?? $currentData['main']['temp'] ?? 0)),
            'icon' => app_weather_icon_name((string) ($currentWeather['icon'] ?? ''), (string) ($currentWeather['description'] ?? '')),
        ),
        'tomorrow' => $tomorrowItem ? array(
            'label' => 'Zítra',
            'description' => ucfirst((string) ($tomorrowWeather['description'] ?? '')),
            'temp' => (int) round((float) ($tomorrowItem['main']['temp'] ?? 0)),
            'night_temp' => (int) round((float) ($tomorrowItem['main']['feels_like'] ?? $tomorrowItem['main']['temp'] ?? 0)),
            'icon' => app_weather_icon_name((string) ($tomorrowWeather['icon'] ?? ''), (string) ($tomorrowWeather['description'] ?? '')),
        ) : null,
    );

    app_ensure_directory('cache');
    @file_put_contents($cachePath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    return $payload;
}
