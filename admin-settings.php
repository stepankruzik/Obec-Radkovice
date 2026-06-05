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

    for ($index = 1; $index <= 4; $index++) {
        app_save_setting("home_slide_{$index}_title", app_post("home_slide_{$index}_title"));
        app_save_setting("home_slide_{$index}_text", app_post("home_slide_{$index}_text"));
        app_save_setting("home_slide_{$index}_badge", app_post("home_slide_{$index}_badge"));

        if (!empty($_POST["home_slide_{$index}_remove"])) {
            app_delete_uploaded_file(app_setting("home_slide_{$index}_image", ''));
            app_save_setting("home_slide_{$index}_image", '');
        }

        try {
            $upload = app_upload_file($_FILES["home_slide_{$index}_image"] ?? array(), 'uploads/home', array('jpg', 'jpeg', 'png', 'webp'));
            if ($upload) {
                app_delete_uploaded_file(app_setting("home_slide_{$index}_image", ''));
                app_save_setting("home_slide_{$index}_image", $upload['path']);
            }
        } catch (RuntimeException $exception) {
            app_set_flash('error', $exception->getMessage());
            app_redirect('admin-settings.php');
        }
    }

    app_set_flash('success', 'Nastaveni bylo ulozeno.');
    app_redirect('admin-settings.php');
}

$pageTitle = 'Admin | Nastaveni';
$adminPageTitle = 'Nastaveni systemu';
$adminPageDescription = 'Zakladni konfigurace webu, kontakty a obsah homepage.';
$adminActiveNav = 'settings';
$adminActionLabel = 'Ulozit zmeny';
$adminActionHref = '#editor';
require_once('includes/admin-header.php');
?>

            <section class="admin-section-grid">
                <section class="admin-panel">
                    <div class="admin-panel-head">
                        <h2>Obecne nastaveni</h2>
                    </div>

                    <form id="editor" class="admin-form-grid" method="post" enctype="multipart/form-data">
                        <label class="admin-field">
                            <span>Nazev webu</span>
                            <input type="text" name="site_name" value="<?php echo app_e(app_setting('site_name', 'Radkovice u Budce')); ?>">
                        </label>
                        <label class="admin-field">
                            <span>Kontaktni e-mail</span>
                            <input type="email" name="contact_email" value="<?php echo app_e(app_setting('contact_email', 'obec_radkovice@volny.cz')); ?>">
                        </label>
                        <label class="admin-field">
                            <span>Telefon</span>
                            <input type="text" name="contact_phone" value="<?php echo app_e(app_setting('contact_phone', '770 132 011')); ?>">
                        </label>
                        <label class="admin-field">
                            <span>ID datove schranky</span>
                            <input type="text" name="databox_id" value="<?php echo app_e(app_setting('databox_id', '9pfj2mc')); ?>">
                        </label>
                        <label class="admin-field admin-field-full">
                            <span>Adresa uradu</span>
                            <textarea name="office_address" rows="4"><?php echo app_e(app_setting('office_address', "Obecni urad\nRadkovice u Budce 14\n380 01 Dacice")); ?></textarea>
                        </label>

                        <label class="admin-field admin-field-full">
                            <span>OpenWeatherMap API klic</span>
                            <input type="text" name="weather_api_key" value="<?php echo app_e(app_setting('weather_api_key', '')); ?>" placeholder="Vlozte API klic">
                        </label>
                        <label class="admin-field">
                            <span>Zemepisna sirka</span>
                            <input type="text" name="weather_lat" value="<?php echo app_e(app_setting('weather_lat', '49.087222')); ?>">
                        </label>
                        <label class="admin-field">
                            <span>Zemepisna delka</span>
                            <input type="text" name="weather_lon" value="<?php echo app_e(app_setting('weather_lon', '15.634444')); ?>">
                        </label>

                        <div class="admin-field admin-field-full">
                            <span>Carousel na uvodni strance</span>
                            <div class="admin-card-list">
                                <?php for ($index = 1; $index <= 4; $index++): ?>
                                    <?php $slideImage = app_setting("home_slide_{$index}_image", $index === 1 ? 'img/uvod.JPG' : ''); ?>
                                    <section class="content-card admin-carousel-card">
                                        <div class="content-card-top">
                                            <strong>Slide <?php echo $index; ?></strong>
                                            <?php if ($slideImage !== ''): ?>
                                                <span>Aktivni snimek</span>
                                            <?php else: ?>
                                                <span>Zatim bez obrazku</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="admin-form-grid">
                                            <label class="admin-field">
                                                <span>Nadpis</span>
                                                <input type="text" name="home_slide_<?php echo $index; ?>_title" value="<?php echo app_e(app_setting("home_slide_{$index}_title", $index === 1 ? 'Vitejte v Radkovicich u Budce' : '')); ?>">
                                            </label>
                                            <label class="admin-field">
                                                <span>Stitek</span>
                                                <input type="text" name="home_slide_<?php echo $index; ?>_badge" value="<?php echo app_e(app_setting("home_slide_{$index}_badge", $index === 1 ? 'Vitejte u nas' : '')); ?>">
                                            </label>
                                            <label class="admin-field admin-field-full">
                                                <span>Popis</span>
                                                <textarea name="home_slide_<?php echo $index; ?>_text" rows="3"><?php echo app_e(app_setting("home_slide_{$index}_text", $index === 1 ? 'Oficialni informacni portal obce. Dokumenty, fotogalerie, kontakty i informace o obci na jednom miste.' : '')); ?></textarea>
                                            </label>
                                            <label class="admin-field admin-field-full">
                                                <span>Obrazek</span>
                                                <input type="file" name="home_slide_<?php echo $index; ?>_image" accept=".jpg,.jpeg,.png,.webp">
                                            </label>
                                            <label class="admin-checkbox admin-field-full">
                                                <input type="checkbox" name="home_slide_<?php echo $index; ?>_remove" value="1">
                                                <span>Smazat aktualni obrazek tohoto slidu</span>
                                            </label>
                                        </div>

                                        <?php if ($slideImage !== ''): ?>
                                            <div class="admin-photo-card">
                                                <img src="<?php echo app_e($slideImage); ?>" alt="Nahled slide <?php echo $index; ?>">
                                                <span>Aktualni obrazek carouselu</span>
                                            </div>
                                        <?php endif; ?>
                                    </section>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="editor-actions editor-actions-full">
                            <button class="admin-button" type="submit" name="save_settings">Ulozit zmeny</button>
                        </div>
                    </form>
                </section>

                <aside class="admin-side-stack">
                    <section class="admin-panel">
                        <h2>Pocasi</h2>
                        <div class="admin-mini-list">
                            <div><strong>Zdroj</strong><span>OpenWeatherMap</span></div>
                            <div><strong>Souradnice</strong><span><?php echo app_e(app_setting('weather_lat', '49.087222')); ?> / <?php echo app_e(app_setting('weather_lon', '15.634444')); ?></span></div>
                            <div><strong>Cache</strong><span>30 minut</span></div>
                        </div>
                    </section>

                    <section class="admin-panel">
                        <h2>Homepage</h2>
                        <div class="admin-mini-list">
                            <div><strong>Carousel</strong><span><?php echo count(app_home_slides()); ?> snimky</span></div>
                            <div><strong>Domaci foto</strong><span>Uprava primo zde</span></div>
                            <div><strong>Doporuceni</strong><span>Nahravejte stejnou orientaci fotek</span></div>
                        </div>
                    </section>
                </aside>
            </section>

<?php require_once('includes/admin-footer.php'); ?>
