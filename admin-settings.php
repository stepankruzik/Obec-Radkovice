<?php
require_once('app.php');
app_require_admin();

if (isset($_POST['save_settings'])) {
    app_save_setting('site_name', app_post('site_name'));
    app_save_setting('contact_email', app_post('contact_email'));
    app_save_setting('contact_phone', app_post('contact_phone'));
    app_save_setting('databox_id', app_post('databox_id'));
    app_save_setting('office_address', trim($_POST['office_address'] ?? ''));
    app_save_setting('weather_api_key', app_post('weather_api_key'));
    app_save_setting('weather_lat', app_post('weather_lat', '49.087222'));
    app_save_setting('weather_lon', app_post('weather_lon', '15.634444'));
    app_set_flash('success', 'Nastavení bylo uloženo.');
    app_redirect('admin-settings.php');
}

$pageTitle = 'Admin | Nastavení';
$adminPageTitle = 'Nastavení systému';
$adminPageDescription = 'Základní konfigurace webu, kontaktní údaje a napojení služeb.';
$adminActiveNav = 'settings';
$adminActionLabel = 'Uložit změny';
$adminActionHref = '#editor';
require_once('includes/admin-header.php');
?>

            <section class="admin-section-grid">
                <section class="admin-panel">
                    <div class="admin-panel-head">
                        <h2>Obecné nastavení</h2>
                    </div>

                    <form id="editor" class="admin-form-grid" method="post">
                        <label class="admin-field">
                            <span>Název webu</span>
                            <input type="text" name="site_name" value="<?php echo app_e(app_setting('site_name', 'Radkovice u Budče')); ?>">
                        </label>
                        <label class="admin-field">
                            <span>Kontaktní e-mail</span>
                            <input type="email" name="contact_email" value="<?php echo app_e(app_setting('contact_email', 'obec_radkovice@volny.cz')); ?>">
                        </label>
                        <label class="admin-field">
                            <span>Telefon</span>
                            <input type="text" name="contact_phone" value="<?php echo app_e(app_setting('contact_phone', '770 132 011')); ?>">
                        </label>
                        <label class="admin-field">
                            <span>ID datové schránky</span>
                            <input type="text" name="databox_id" value="<?php echo app_e(app_setting('databox_id', '9pfj2mc')); ?>">
                        </label>
                        <label class="admin-field admin-field-full">
                            <span>Adresa úřadu</span>
                            <textarea name="office_address" rows="4"><?php echo app_e(app_setting('office_address', "Obecní úřad\nRadkovice u Budče 14\n380 01 Dačice")); ?></textarea>
                        </label>

                        <label class="admin-field admin-field-full">
                            <span>OpenWeatherMap API klíč</span>
                            <input type="text" name="weather_api_key" value="<?php echo app_e(app_setting('weather_api_key', '')); ?>" placeholder="Vložte API klíč">
                        </label>
                        <label class="admin-field">
                            <span>Zeměpisná šířka</span>
                            <input type="text" name="weather_lat" value="<?php echo app_e(app_setting('weather_lat', '49.087222')); ?>">
                        </label>
                        <label class="admin-field">
                            <span>Zeměpisná délka</span>
                            <input type="text" name="weather_lon" value="<?php echo app_e(app_setting('weather_lon', '15.634444')); ?>">
                        </label>

                        <div class="editor-actions editor-actions-full">
                            <button class="admin-button" type="submit" name="save_settings">Uložit změny</button>
                        </div>
                    </form>
                </section>

                <aside class="admin-side-stack">
                    <section class="admin-panel">
                        <h2>Počasí</h2>
                        <div class="admin-mini-list">
                            <div><strong>Zdroj</strong><span>OpenWeatherMap</span></div>
                            <div><strong>Souřadnice</strong><span>49.087222 / 15.634444</span></div>
                            <div><strong>Cache</strong><span>30 minut</span></div>
                        </div>
                    </section>

                    <section class="admin-panel">
                        <h2>Údržba</h2>
                        <div class="admin-mini-list">
                            <div><strong>Poslední záloha</strong><span>Dnes 02:00</span></div>
                            <div><strong>Verze systému</strong><span>1.0.0</span></div>
                            <div><strong>Stav databáze</strong><span>V pořádku</span></div>
                        </div>
                    </section>
                </aside>
            </section>

<?php require_once('includes/admin-footer.php'); ?>
