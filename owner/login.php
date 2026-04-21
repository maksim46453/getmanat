<?php
require_once __DIR__ . '/admin_common.php';

if (owner_is_authenticated()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!owner_login_attempt_allowed()) {
        $error = 'Забагато спроб. Спробуйте пізніше.';
    } elseif (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Невірний CSRF токен.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (hash_equals(OWNER_ADMIN_USERNAME, $username) && password_verify($password, OWNER_ADMIN_PASSWORD_HASH)) {
            session_regenerate_id(true);
            $_SESSION['owner_logged_in'] = true;
            $_SESSION['owner_user'] = OWNER_ADMIN_USERNAME;
            unset($_SESSION['owner_login_attempts']);
            header('Location: index.php');
            exit;
        }

        owner_register_login_attempt();
        $error = 'Невірні дані входу.';
    }
}

owner_render_header('Login');
?>
<div class="portal-card mx-auto" style="max-width:480px;">
    <h2 class="section-title">Вхід до owner panel</h2>
    <?php if ($error !== ''): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" name="username" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Увійти</button>
    </form>
</div>
<?php owner_render_footer();
