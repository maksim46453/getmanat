<?php
require_once __DIR__ . '/../includes/common.php';
require_once __DIR__ . '/../class/database.php';
require_once __DIR__ . '/admin_config.php';

function owner_db(): DataBase
{
    static $db = null;
    if ($db === null) {
        $db = new DataBase();
        $db->query('CREATE TABLE IF NOT EXISTS roulette_rewards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            roulette_type ENUM("free","advanced") NOT NULL,
            reward_name VARCHAR(128) NOT NULL,
            reward_type VARCHAR(64) NOT NULL,
            duration_hours INT NOT NULL DEFAULT 0,
            chance_percent DECIMAL(5,2) NOT NULL,
            is_enabled TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_type_enabled(roulette_type, is_enabled)
        )');

        $existing = $db->select('SELECT COUNT(*) AS cnt FROM roulette_rewards');
        if ((int) ($existing[0]['cnt'] ?? 0) === 0) {
            $seedRewards = [
                ['free', 'VIP 24h', 'vip', 24, 30.0, 1],
                ['free', 'Буст XP', 'booster', 1, 45.0, 1],
                ['free', 'Спрей-пак', 'cosmetic', 0, 25.0, 1],
                ['advanced', 'Premium VIP 72h', 'premium_vip', 72, 20.0, 1],
                ['advanced', 'VIP 168h', 'vip', 168, 35.0, 1],
                ['advanced', 'Рідкісний бонус', 'bonus', 0, 45.0, 1],
            ];
            foreach ($seedRewards as $reward) {
                $db->query('INSERT INTO roulette_rewards (roulette_type, reward_name, reward_type, duration_hours, chance_percent, is_enabled) VALUES (:type, :name, :reward_type, :duration, :chance, :enabled)', [
                    'type' => $reward[0],
                    'name' => $reward[1],
                    'reward_type' => $reward[2],
                    'duration' => $reward[3],
                    'chance' => $reward[4],
                    'enabled' => $reward[5],
                ]);
            }
        }
    }

    return $db;
}

function owner_login_attempt_allowed(): bool
{
    $bucket = 'owner_login_attempts';
    $windowSeconds = 15 * 60;
    $maxAttempts = 8;

    if (!isset($_SESSION[$bucket]) || !is_array($_SESSION[$bucket])) {
        $_SESSION[$bucket] = [];
    }

    $now = time();
    $_SESSION[$bucket] = array_values(array_filter($_SESSION[$bucket], static fn($ts) => ($now - (int) $ts) <= $windowSeconds));

    return count($_SESSION[$bucket]) < $maxAttempts;
}

function owner_register_login_attempt(): void
{
    $_SESSION['owner_login_attempts'][] = time();
}

function owner_is_authenticated(): bool
{
    return !empty($_SESSION['owner_logged_in']) && !empty($_SESSION['owner_user']);
}

function owner_require_auth(): void
{
    if (!owner_is_authenticated()) {
        header('Location: login.php');
        exit;
    }
}

function owner_render_header(string $title): void
{
    ?>
    <!doctype html>
    <html lang="uk">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo e($title); ?> — Owner Panel</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link rel="stylesheet" href="../style.css">
    </head>
    <body class="owner-body">
    <main class="container py-4">
        <div class="portal-card mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h1 class="section-title mb-0">Owner Panel</h1>
                    <p class="text-secondary mb-0">Прихована адмін-зона для керування рулеткою та аналітикою.</p>
                </div>
                <?php if (owner_is_authenticated()): ?>
                    <div class="d-flex gap-2">
                        <a href="index.php" class="btn btn-outline-light btn-sm">Головна адмін</a>
                        <a href="rewards.php" class="btn btn-outline-light btn-sm">Roulette Rewards</a>
                        <a href="analytics.php" class="btn btn-outline-light btn-sm">Analytics</a>
                        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php
}

function owner_render_footer(): void
{
    ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    </body>
    </html>
    <?php
}
