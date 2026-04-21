<?php
require_once __DIR__ . '/includes/common.php';
require_once __DIR__ . '/class/database.php';
require_once __DIR__ . '/steamauth/steamauth.php';
require_once __DIR__ . '/class/utils.php';
require_once __DIR__ . '/includes/tracker.php';

track_visit('skinchanger.php');

$db = new DataBase();
$skinError = '';

if (isset($_SESSION['steamid'])) {
    $steamid = $_SESSION['steamid'];

    $weapons = UtilsClass::getWeaponsFromArray();
    $skins = UtilsClass::skinsFromJson();
    $querySelected = $db->select(
        'SELECT `weapon_defindex`, MAX(`weapon_paint_id`) AS `weapon_paint_id`, MAX(`weapon_wear`) AS `weapon_wear`, MAX(`weapon_seed`) AS `weapon_seed`
         FROM `wp_player_skins`
         WHERE `steamid` = :steamid
         GROUP BY `weapon_defindex`, `steamid`',
        ['steamid' => $steamid]
    );
    $selectedSkins = UtilsClass::getSelectedSkins($querySelected);
    $selectedKnife = $db->select('SELECT * FROM `wp_player_knife` WHERE `wp_player_knife`.`steamid` = :steamid LIMIT 1', ['steamid' => $steamid]);
    $knifes = UtilsClass::getKnifeTypes();

    if (isset($_POST['forma'])) {
        $token = $_POST['csrf_token'] ?? null;
        if (!verify_csrf($token)) {
            $skinError = 'Помилка безпеки: недійсний CSRF токен.';
        } else {
            $ex = explode('-', $_POST['forma']);
            if ($ex[0] === 'knife') {
                $knifeName = $knifes[$ex[1]]['weapon_name'] ?? null;
                if ($knifeName !== null) {
                    $db->query('INSERT INTO `wp_player_knife` (`steamid`, `knife`, `weapon_team`) VALUES(:steamid, :knife, 2) ON DUPLICATE KEY UPDATE `knife` = :knife', ['steamid' => $steamid, 'knife' => $knifeName]);
                    $db->query('INSERT INTO `wp_player_knife` (`steamid`, `knife`, `weapon_team`) VALUES(:steamid, :knife, 3) ON DUPLICATE KEY UPDATE `knife` = :knife', ['steamid' => $steamid, 'knife' => $knifeName]);
                }
            } else {
                if (array_key_exists($ex[1], $skins[$ex[0]] ?? []) && isset($_POST['wear']) && $_POST['wear'] >= 0.00 && $_POST['wear'] <= 1.00 && isset($_POST['seed'])) {
                    $wear = floatval($_POST['wear']);
                    $seed = intval($_POST['seed']);
                    if (array_key_exists($ex[0], $selectedSkins)) {
                        $db->query('UPDATE wp_player_skins SET weapon_paint_id = :weapon_paint_id, weapon_wear = :weapon_wear, weapon_seed = :weapon_seed WHERE steamid = :steamid AND weapon_defindex = :weapon_defindex', ['steamid' => $steamid, 'weapon_defindex' => $ex[0], 'weapon_paint_id' => $ex[1], 'weapon_wear' => $wear, 'weapon_seed' => $seed]);
                    } else {
                        $db->query('INSERT INTO wp_player_skins (`steamid`, `weapon_defindex`, `weapon_paint_id`, `weapon_wear`, `weapon_seed`, `weapon_team`) VALUES (:steamid, :weapon_defindex, :weapon_paint_id, :weapon_wear, :weapon_seed, 2)', ['steamid' => $steamid, 'weapon_defindex' => $ex[0], 'weapon_paint_id' => $ex[1], 'weapon_wear' => $wear, 'weapon_seed' => $seed]);
                        $db->query('INSERT INTO wp_player_skins (`steamid`, `weapon_defindex`, `weapon_paint_id`, `weapon_wear`, `weapon_seed`, `weapon_team`) VALUES (:steamid, :weapon_defindex, :weapon_paint_id, :weapon_wear, :weapon_seed, 3)', ['steamid' => $steamid, 'weapon_defindex' => $ex[0], 'weapon_paint_id' => $ex[1], 'weapon_wear' => $wear, 'weapon_seed' => $seed]);
                    }
                }
            }

            if ($skinError === '') {
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    }
}

render_header('SkinChanger', 'Налаштування скіна, ножа, wear та seed для [UA] Народний Паблік');
?>
<main class="container py-4">
    <?php if (!isset($_SESSION['steamid'])): ?>
        <section class="portal-card text-center">
            <h1 class="section-title">SkinChanger</h1>
            <p>Щоб налаштувати скіни, увійдіть через Steam.</p>
            <?php loginbutton('rectangle'); ?>
        </section>
    <?php else: ?>
        <section class="portal-card mb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="section-title mb-1">Ваш поточний SkinChanger loadout</h1>
                <p class="mb-0 text-secondary">SteamID: <?php echo e((string) $_SESSION['steamid']); ?></p>
            </div>
            <a class="btn btn-danger" href="<?php echo e($_SERVER['PHP_SELF']); ?>?logout">Logout</a>
        </section>

        <?php if ($skinError !== ''): ?>
            <div class="alert alert-danger"><?php echo e($skinError); ?></div>
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="portal-card h-100">
                    <?php
                    $actualKnife = $knifes[0];
                    if ($selectedKnife != null) {
                        foreach ($knifes as $knife) {
                            if (($selectedKnife[0]['knife'] ?? '') === $knife['weapon_name']) {
                                $actualKnife = $knife;
                                break;
                            }
                        }
                    }
                    ?>
                    <h3 class="item-name">Knife type</h3>
                    <p class="text-info"><?php echo e($actualKnife['paint_name']); ?></p>
                    <img src="<?php echo e($actualKnife['image_url']); ?>" class="skin-image" alt="Knife preview">
                    <form action="" method="POST" class="mt-3">
                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                        <select name="forma" class="form-select" onchange="this.form.submit()">
                            <option disabled>Select knife</option>
                            <?php foreach ($knifes as $knifeKey => $knife): ?>
                                <option <?php echo (($selectedKnife[0]['knife'] ?? '') === $knife['weapon_name']) ? 'selected' : ''; ?> value="knife-<?php echo e((string) $knifeKey); ?>">
                                    <?php echo e($knife['paint_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>

            <?php foreach ($weapons as $defindex => $default): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="portal-card h-100 d-flex flex-column">
                        <?php if (array_key_exists($defindex, $selectedSkins)): ?>
                            <h3 class="item-name"><?php echo e($skins[$defindex][$selectedSkins[$defindex]['weapon_paint_id']]['paint_name']); ?></h3>
                            <img src="<?php echo e($skins[$defindex][$selectedSkins[$defindex]['weapon_paint_id']]['image_url']); ?>" class="skin-image" alt="Skin preview">
                        <?php else: ?>
                            <h3 class="item-name"><?php echo e($default['paint_name']); ?></h3>
                            <img src="<?php echo e($default['image_url']); ?>" class="skin-image" alt="Skin preview">
                        <?php endif; ?>

                        <form action="" method="POST" class="mt-3 mt-auto">
                            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                            <select name="forma" class="form-select" onchange="this.form.submit()">
                                <option disabled>Select skin</option>
                                <?php foreach ($skins[$defindex] as $paintKey => $paint): ?>
                                    <option <?php echo (array_key_exists($defindex, $selectedSkins) && $selectedSkins[$defindex]['weapon_paint_id'] == $paintKey) ? 'selected' : ''; ?> value="<?php echo e((string) $defindex); ?>-<?php echo e((string) $paintKey); ?>">
                                        <?php echo e($paint['paint_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php
                            $selectedSkinInfo = $selectedSkins[$defindex] ?? null;
                            $initialWearValue = $selectedSkinInfo['weapon_wear'] ?? 1.0;
                            $initialSeedValue = $selectedSkinInfo['weapon_seed'] ?? 0;
                            ?>
                            <div class="mt-3 d-grid">
                                <?php if ($selectedSkinInfo): ?>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#weaponModal<?php echo e((string) $defindex); ?>">Settings</button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary" onclick="alert('You need to select a skin first.')">Settings</button>
                                <?php endif; ?>
                            </div>

                            <div class="modal fade" id="weaponModal<?php echo e((string) $defindex); ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content bg-dark text-light">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <?php echo e((array_key_exists($defindex, $selectedSkins) ? $skins[$defindex][$selectedSkins[$defindex]['weapon_paint_id']]['paint_name'] : $default['paint_name']) . ' Settings'); ?>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Wear preset</label>
                                                <select class="form-select" onchange="document.getElementById('wear<?php echo e((string) $defindex); ?>').value = this.value;">
                                                    <option disabled>Select Wear</option>
                                                    <option value="0.00" <?php echo ($initialWearValue == 0.00) ? 'selected' : ''; ?>>Factory New</option>
                                                    <option value="0.07" <?php echo ($initialWearValue == 0.07) ? 'selected' : ''; ?>>Minimal Wear</option>
                                                    <option value="0.15" <?php echo ($initialWearValue == 0.15) ? 'selected' : ''; ?>>Field-Tested</option>
                                                    <option value="0.38" <?php echo ($initialWearValue == 0.38) ? 'selected' : ''; ?>>Well-Worn</option>
                                                    <option value="0.45" <?php echo ($initialWearValue == 0.45) ? 'selected' : ''; ?>>Battle-Scarred</option>
                                                </select>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <label class="form-label" for="wear<?php echo e((string) $defindex); ?>">Wear</label>
                                                    <input type="text" class="form-control" id="wear<?php echo e((string) $defindex); ?>" name="wear" value="<?php echo e((string) $initialWearValue); ?>">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label" for="seed<?php echo e((string) $defindex); ?>">Seed</label>
                                                    <input type="text" class="form-control" id="seed<?php echo e((string) $defindex); ?>" name="seed" value="<?php echo e((string) $initialSeedValue); ?>" oninput="this.value=this.value.replace(/[^0-9]/g, '').slice(0,4)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger">Use</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php render_footer(); ?>
