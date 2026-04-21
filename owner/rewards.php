<?php
require_once __DIR__ . '/admin_common.php';
owner_require_auth();
$db = owner_db();

$message = '';
$error = '';

if (!$db->isConnected()) {
    $error = 'Немає підключення до БД. Налаштуй class/config.php для роботи адмінки.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Невірний CSRF токен.';
    } else {
        $action = $_POST['action'] ?? '';
        $rouletteType = ($_POST['roulette_type'] ?? '') === 'advanced' ? 'advanced' : 'free';
        $rewardName = trim((string) ($_POST['reward_name'] ?? ''));
        $rewardType = trim((string) ($_POST['reward_type'] ?? 'bonus'));
        $duration = max(0, min(8760, (int) ($_POST['duration_hours'] ?? 0)));
        $chance = max(0, min(100, (float) ($_POST['chance_percent'] ?? 0)));
        $enabled = isset($_POST['is_enabled']) ? 1 : 0;

        if ($action === 'add') {
            if ($rewardName === '' || strlen($rewardName) > 128) {
                $error = 'Вкажіть коректну назву нагороди (1-128 символів).';
            } else {
                $db->query('INSERT INTO roulette_rewards (roulette_type, reward_name, reward_type, duration_hours, chance_percent, is_enabled) VALUES (:type, :name, :reward_type, :duration, :chance, :enabled)', [
                    'type' => $rouletteType, 'name' => $rewardName, 'reward_type' => $rewardType,
                    'duration' => $duration, 'chance' => $chance, 'enabled' => $enabled,
                ]);
                $message = 'Нагороду додано.';
            }
        }

        if ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0 && $rewardName !== '') {
                $db->query('UPDATE roulette_rewards SET roulette_type = :type, reward_name = :name, reward_type = :reward_type, duration_hours = :duration, chance_percent = :chance, is_enabled = :enabled WHERE id = :id', [
                    'id' => $id, 'type' => $rouletteType, 'name' => $rewardName, 'reward_type' => $rewardType,
                    'duration' => $duration, 'chance' => $chance, 'enabled' => $enabled,
                ]);
                $message = 'Нагороду оновлено.';
            } else {
                $error = 'Некоректні дані для оновлення.';
            }
        }
    }
}

$rewards = $db->isConnected() ? $db->select('SELECT * FROM roulette_rewards ORDER BY roulette_type, id') : [];
owner_render_header('Roulette Rewards');
?>
<div class="portal-card mb-3"><h2 class="section-title">Додати нагороду</h2><?php if ($message !== ''): ?><div class="alert alert-success"><?php echo e($message); ?></div><?php endif; ?><?php if ($error !== ''): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
<form class="row g-2" method="post"><input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>"><input type="hidden" name="action" value="add"><div class="col-md-2"><select class="form-select" name="roulette_type"><option value="free">Free</option><option value="advanced">Advanced</option></select></div><div class="col-md-3"><input class="form-control" name="reward_name" placeholder="Reward name" required></div><div class="col-md-2"><input class="form-control" name="reward_type" placeholder="Type (vip, bonus...)" required></div><div class="col-md-2"><input class="form-control" type="number" name="duration_hours" min="0" max="8760" placeholder="Duration h"></div><div class="col-md-2"><input class="form-control" type="number" step="0.01" name="chance_percent" min="0" max="100" placeholder="Chance %" required></div><div class="col-md-1 d-flex align-items-center"><input class="form-check-input" type="checkbox" name="is_enabled" checked></div><div class="col-12"><button class="btn btn-primary" type="submit" <?php echo !$db->isConnected() ? 'disabled' : ''; ?>>Додати</button></div></form></div>

<div class="portal-card"><h2 class="section-title">Керування нагородами</h2><div class="table-responsive"><table class="table table-dark table-striped align-middle"><thead><tr><th>ID</th><th>Type</th><th>Name</th><th>Reward Type</th><th>Duration</th><th>Chance %</th><th>Enabled</th><th>Action</th></tr></thead><tbody>
<?php foreach ($rewards as $reward): ?><tr><form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?php echo e((string) $reward['id']); ?>"><td><?php echo e((string) $reward['id']); ?></td><td><select name="roulette_type" class="form-select form-select-sm"><option value="free" <?php echo $reward['roulette_type'] === 'free' ? 'selected' : ''; ?>>free</option><option value="advanced" <?php echo $reward['roulette_type'] === 'advanced' ? 'selected' : ''; ?>>advanced</option></select></td><td><input class="form-control form-control-sm" name="reward_name" value="<?php echo e($reward['reward_name']); ?>" required></td><td><input class="form-control form-control-sm" name="reward_type" value="<?php echo e($reward['reward_type']); ?>" required></td><td><input class="form-control form-control-sm" type="number" name="duration_hours" min="0" max="8760" value="<?php echo e((string) $reward['duration_hours']); ?>"></td><td><input class="form-control form-control-sm" type="number" step="0.01" name="chance_percent" min="0" max="100" value="<?php echo e((string) $reward['chance_percent']); ?>" required></td><td><input class="form-check-input" type="checkbox" name="is_enabled" <?php echo (int) $reward['is_enabled'] === 1 ? 'checked' : ''; ?>></td><td><button class="btn btn-sm btn-primary" type="submit">Save</button></td></form></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php owner_render_footer();
