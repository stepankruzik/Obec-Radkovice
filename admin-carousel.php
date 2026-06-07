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

if (isset($_POST['save_hero_slide_order'])) {
    $slides = app_setting_json('hero_carousel', array());
    $postedOrder = $_POST['slide_order'] ?? array();

    if (!is_array($postedOrder)) {
        $postedOrder = array();
    }

    $postedOrder = array_map('intval', $postedOrder);
    $postedOrder = array_values(array_unique($postedOrder));
    $slideIndexes = array_keys($slides);
    sort($postedOrder);
    $expectedOrder = $slideIndexes;
    sort($expectedOrder);

    if ($slides && $postedOrder === $expectedOrder) {
        $reorderedSlides = array();
        foreach ($_POST['slide_order'] as $slideIndex) {
            $slideIndex = (int) $slideIndex;
            $reorderedSlides[] = $slides[$slideIndex];
        }

        app_save_setting('hero_carousel', json_encode(array_values($reorderedSlides), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        app_set_flash('success', 'Pořadí slidů bylo upraveno.');
    } else {
        app_set_flash('error', 'Pořadí slidů se nepodařilo uložit.');
    }

    app_redirect('admin-carousel.php#hero-carousel-editor');
}

if (isset($_POST['remove_hero_slide'])) {
    $index = (int) $_POST['remove_hero_slide'];
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

                    <div class="admin-carousel-list-wrap">
                        <div class="admin-carousel-help">
                            Přetáhněte fotky myší a pak klikněte na uložit pořadí.
                        </div>

                        <form method="post" class="admin-carousel-order-form" data-carousel-sort-form>
                            <div class="admin-carousel-list" data-carousel-sortable>
                                <?php if ($heroSlides): ?>
                                    <?php foreach ($heroSlides as $index => $slide): ?>
                                        <article class="admin-carousel-item" data-slide-item draggable="true">
                                            <input type="hidden" name="slide_order[]" value="<?php echo (int) $index; ?>" data-slide-order>
                                            <button class="admin-carousel-handle" type="button" aria-label="Přetáhnout slide" title="Přetáhnout slide">⋮⋮</button>
                                            <img src="<?php echo app_e($slide['image'] ?? ''); ?>" alt="<?php echo app_e($slide['title'] ?? 'Slide homepage'); ?>">
                                            <div class="admin-carousel-copy">
                                                <strong><?php echo ($index + 1) . '. ' . app_e($slide['title'] ?? 'Bez nadpisu'); ?></strong>
                                                <span><?php echo app_e($slide['text'] ?? ''); ?></span>
                                            </div>
                                            <div class="admin-carousel-actions">
                                                <button class="action-link action-link-danger" type="submit" name="remove_hero_slide" value="<?php echo (int) $index; ?>">Odebrat</button>
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

                            <?php if ($heroSlides): ?>
                                <div class="editor-actions editor-actions-full">
                                    <button class="admin-button" type="submit" name="save_hero_slide_order">Uložit pořadí</button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </section>

<?php require_once('includes/admin-footer.php'); ?>
