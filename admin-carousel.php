<?php
require_once('app.php');
app_require_admin();

if (isset($_POST['add_hero_slide'])) {
    try {
        $upload = app_upload_file($_FILES['slide_image'] ?? array(), 'uploads/hero-slides', array('jpg', 'jpeg', 'png', 'webp'));
        if (!$upload) {
            throw new RuntimeException('Vyberte obrázek pro nový slide.');
        }

        $slides = app_setting_json('hero_carousel', array());
        $slides[] = array(
            'image' => $upload['path'],
            'title' => app_post('slide_title'),
            'text' => app_post('slide_text'),
        );

        app_save_setting('hero_carousel', json_encode($slides, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        app_set_flash('success', 'Slide na homepage byl přidán.');
    } catch (RuntimeException $exception) {
        app_set_flash('error', $exception->getMessage());
    }

    app_redirect('admin-carousel.php#hero-carousel-editor');
}

if (isset($_POST['remove_hero_slide'])) {
    $index = (int) ($_POST['slide_index'] ?? -1);
    $slides = app_setting_json('hero_carousel', array());

    if (isset($slides[$index])) {
        $imagePath = (string) ($slides[$index]['image'] ?? '');
        if (strpos($imagePath, 'uploads/hero-slides/') === 0) {
            app_delete_uploaded_file($imagePath);
        }

        array_splice($slides, $index, 1);
        app_save_setting('hero_carousel', json_encode(array_values($slides), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        app_set_flash('success', 'Slide na homepage byl odebrán.');
    }

    app_redirect('admin-carousel.php#hero-carousel-editor');
}

if (isset($_POST['move_hero_slide'])) {
    $index = (int) ($_POST['slide_index'] ?? -1);
    $direction = $_POST['direction'] ?? '';
    $slides = app_setting_json('hero_carousel', array());
    $targetIndex = $direction === 'up' ? $index - 1 : $index + 1;

    if (isset($slides[$index], $slides[$targetIndex])) {
        $current = $slides[$index];
        $slides[$index] = $slides[$targetIndex];
        $slides[$targetIndex] = $current;
        app_save_setting('hero_carousel', json_encode(array_values($slides), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        app_set_flash('success', 'Pořadí slidů bylo upraveno.');
    }

    app_redirect('admin-carousel.php#hero-carousel-editor');
}

$heroSlides = app_setting_json('hero_carousel', array());

$pageTitle = 'Carousel homepage';
$adminPageTitle = 'Carousel homepage';
$adminPageDescription = 'Správa hlavních fotografií a pořadí slidů na úvodní stránce.';
$adminActiveNav = 'carousel';
$adminActionLabel = '+ Přidat slide';
$adminActionHref = '#hero-carousel-editor';
require_once('includes/admin-header.php');
?>

            <section class="admin-panel admin-carousel-manager" id="hero-carousel-editor">
                <div class="admin-panel-head">
                    <h2>Carousel homepage</h2>
                    <a href="index.php" target="_blank" rel="noreferrer">Zobrazit web</a>
                </div>

                <div class="admin-carousel-grid">
                    <div>
                        <form class="admin-form-grid" method="post" enctype="multipart/form-data">
                            <label class="admin-field admin-field-full">
                                <span>Obrázek slide</span>
                                <input type="file" name="slide_image" accept=".jpg,.jpeg,.png,.webp" required>
                            </label>
                            <label class="admin-field">
                                <span>Nadpis</span>
                                <input type="text" name="slide_title" placeholder="Např. Vítejte v Radkovicích u Budče">
                            </label>
                            <label class="admin-field">
                                <span>Popis</span>
                                <input type="text" name="slide_text" placeholder="Krátký text pro homepage">
                            </label>
                            <div class="editor-actions editor-actions-full">
                                <button class="admin-button" type="submit" name="add_hero_slide">Přidat slide</button>
                            </div>
                        </form>
                    </div>

                    <div class="admin-carousel-list">
                        <?php if ($heroSlides): ?>
                            <?php foreach ($heroSlides as $index => $slide): ?>
                                <article class="admin-carousel-item">
                                    <img src="<?php echo app_e($slide['image'] ?? ''); ?>" alt="<?php echo app_e($slide['title'] ?? 'Slide homepage'); ?>">
                                    <div class="admin-carousel-copy">
                                        <strong><?php echo ($index + 1) . '. ' . app_e($slide['title'] ?? 'Bez nadpisu'); ?></strong>
                                        <span><?php echo app_e($slide['text'] ?? ''); ?></span>
                                    </div>
                                    <div class="admin-carousel-actions">
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="slide_index" value="<?php echo (int) $index; ?>">
                                            <input type="hidden" name="direction" value="up">
                                            <button class="action-link" type="submit" name="move_hero_slide" <?php echo $index === 0 ? 'disabled' : ''; ?>>Nahoru</button>
                                        </form>
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="slide_index" value="<?php echo (int) $index; ?>">
                                            <input type="hidden" name="direction" value="down">
                                            <button class="action-link" type="submit" name="move_hero_slide" <?php echo $index === count($heroSlides) - 1 ? 'disabled' : ''; ?>>Dolů</button>
                                        </form>
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="slide_index" value="<?php echo (int) $index; ?>">
                                            <button class="action-link action-link-danger" type="submit" name="remove_hero_slide">Odebrat</button>
                                        </form>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="admin-upload-empty">
                                <strong>Zatím nejsou přidané žádné slidy.</strong>
                                <span>Pokud nic nenastavíte, homepage použije výchozí úvodní fotografii.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

<?php require_once('includes/admin-footer.php'); ?>
