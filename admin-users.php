<?php
require_once('app.php');
app_require_admin();

if (isset($_POST['save_user'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $password = $_POST['password'] ?? '';
    $data = array(
        'name' => app_post('name'),
        'username' => app_post('username'),
        'email' => app_post('email'),
        'role' => app_post('role', 'editor'),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'updated_at' => app_now(),
    );

    if ($id > 0) {
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        Db::update('users', $data, 'WHERE id = ?', $id);
        app_set_flash('success', 'Uživatel byl upraven.');
    } else {
        $data['password'] = password_hash($password !== '' ? $password : 'admin123', PASSWORD_DEFAULT);
        $data['created_at'] = app_now();
        Db::insert('users', $data);
        app_set_flash('success', 'Uživatel byl přidán.');
    }

    app_redirect('admin-users.php');
}

if (isset($_POST['delete_user'])) {
    $id = (int) $_POST['id'];
    if ($id === (int) $_SESSION['user_id']) {
        app_set_flash('error', 'Nelze smazat právě přihlášený účet.');
    } else {
        app_delete_by_id('users', $id);
        app_set_flash('success', 'Uživatel byl smazán.');
    }
    app_redirect('admin-users.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = $editId > 0 ? app_find_by_id('users', $editId) : null;
$users = app_fetch_all('users', 'id DESC');

$pageTitle = 'Admin | Uživatelé';
$adminPageTitle = 'Správa uživatelů';
$adminPageDescription = 'Role, přístupy a přehled uživatelských účtů administrace.';
$adminActiveNav = 'users';
$adminActionLabel = $editing ? 'Upravuji uživatele' : '+ Nový uživatel';
$adminActionHref = '#editor';
require_once('includes/admin-header.php');
?>

            <section class="admin-panel">
                <div class="admin-panel-head">
                    <h2>Uživatelské účty</h2>
                    <a href="#editor"><?php echo $editing ? 'Editace uživatele' : 'Nový uživatel'; ?></a>
                </div>

                <div class="admin-table">
                    <div class="admin-table-head admin-table-head-users">
                        <span>Uživatel</span>
                        <span>Role</span>
                        <span>E-mail</span>
                        <span>Poslední přihlášení</span>
                        <span>Akce</span>
                    </div>

                    <?php foreach ($users as $user): ?>
                        <article class="admin-table-row admin-table-row-users">
                            <div class="admin-doc-title">
                                <span class="mini-doc-icon mini-user-icon"><?php echo app_e(strtoupper(substr($user['name'], 0, 2))); ?></span>
                                <div class="admin-doc-copy">
                                    <strong><?php echo app_e($user['name']); ?></strong>
                                    <span>@<?php echo app_e($user['username']); ?></span>
                                </div>
                            </div>
                            <span class="pill pill-blue"><?php echo app_e($user['role'] === 'admin' ? 'Administrátor' : 'Editor'); ?></span>
                            <span class="table-wrap"><?php echo app_e($user['email']); ?></span>
                            <span><?php echo app_e($user['last_login'] ? date('d. m. Y H:i', strtotime($user['last_login'])) : 'Nikdy'); ?></span>
                            <div class="row-actions row-actions-forms">
                                <a class="action-link" href="admin-users.php?edit=<?php echo (int) $user['id']; ?>#editor" aria-label="Upravit uživatele">✎</a>
                                <form class="inline-form" method="post" onsubmit="return confirm('Opravdu smazat uživatele?');">
                                    <input type="hidden" name="id" value="<?php echo (int) $user['id']; ?>">
                                    <button class="action-button" type="submit" name="delete_user" aria-label="Smazat uživatele">🗑</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <form id="editor" class="admin-editor" method="post">
                    <input type="hidden" name="id" value="<?php echo (int) ($editing['id'] ?? 0); ?>">
                    <div class="admin-panel-head">
                        <h2><?php echo $editing ? 'Upravit uživatele' : 'Přidat uživatele'; ?></h2>
                    </div>
                    <div class="admin-form-grid">
                        <label class="admin-field">
                            <span>Jméno</span>
                            <input type="text" name="name" value="<?php echo app_e($editing['name'] ?? ''); ?>" required>
                        </label>
                        <label class="admin-field">
                            <span>Uživatelské jméno</span>
                            <input type="text" name="username" value="<?php echo app_e($editing['username'] ?? ''); ?>" required>
                        </label>
                        <label class="admin-field">
                            <span>E-mail</span>
                            <input type="email" name="email" value="<?php echo app_e($editing['email'] ?? ''); ?>">
                        </label>
                        <label class="admin-field">
                            <span>Role</span>
                            <select name="role">
                                <?php foreach (array('admin' => 'Administrátor', 'editor' => 'Editor') as $value => $label): ?>
                                    <option value="<?php echo $value; ?>" <?php echo (($editing['role'] ?? 'editor') === $value) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="admin-field admin-field-full">
                            <span>Heslo <?php echo $editing ? '(ponechte prázdné pro beze změny)' : ''; ?></span>
                            <input type="password" name="password" <?php echo $editing ? '' : 'required'; ?>>
                        </label>
                        <label class="admin-checkbox">
                            <input type="checkbox" name="is_active" <?php echo !isset($editing['is_active']) || (int) $editing['is_active'] === 1 ? 'checked' : ''; ?>>
                            <span>Aktivní účet</span>
                        </label>
                    </div>
                    <div class="editor-actions">
                        <button class="admin-button" type="submit" name="save_user">Uložit uživatele</button>
                        <?php if ($editing): ?>
                            <a class="secondary-link" href="admin-users.php">Zrušit editaci</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

<?php require_once('includes/admin-footer.php'); ?>
