<?php
require_once __DIR__ . '/includes/common.php';
require_once __DIR__ . '/includes/tracker.php';

track_visit('index.php');
render_header('Основна', 'Преміум-портал сервера [UA] Народний Паблік');
?>

<main class="container py-4">
    <section class="hero-card mb-4">
        <div class="hero-content">
            <p class="eyebrow">CS2 COMMUNITY PORTAL</p>
            <h1>[UA] Народний Паблік</h1>
            <p class="lead">Народний паблік для відпочинку. Заходь на сервер, збирай ігровий час, крути рулетку та налаштовуй унікальний SkinChanger.</p>
            <div class="hero-actions">
                <a class="btn btn-primary btn-lg" href="steam://connect/91.211.118.100:27119">Підключитись: 91.211.118.100:27119</a>
                <a class="btn btn-outline-light btn-lg" href="skinchanger.php">Відкрити SkinChanger</a>
                <a class="btn btn-outline-light btn-lg" href="skinchanger.php?login">Steam Login</a>
            </div>
        </div>
        <div class="hero-stats">
            <div class="stat">
                <span>Сервер</span>
                <strong>Public CS2</strong>
            </div>
            <div class="stat">
                <span>Мова</span>
                <strong>Українська</strong>
            </div>
            <div class="stat">
                <span>Режим</span>
                <strong>Народний паблік</strong>
            </div>
        </div>
    </section>

    <section class="row g-4 mb-4">
        <div class="col-lg-4">
            <article class="portal-card h-100">
                <h3>SkinChanger</h3>
                <p>Під Steam-акаунтом керуй скінами, ножем, wear та seed без змін серверної логіки.</p>
                <a class="btn btn-sm btn-primary" href="skinchanger.php">До SkinChanger</a>
            </article>
        </div>
        <div class="col-lg-4">
            <article class="portal-card h-100">
                <h3>Донат-магазин</h3>
                <p>VIP, Premium VIP та додаткові ігрові переваги в одному охайному каталозі.</p>
                <a class="btn btn-sm btn-primary" href="donate.php">Переглянути донат</a>
            </article>
        </div>
        <div class="col-lg-4">
            <article class="portal-card h-100">
                <h3>Рулетка</h3>
                <p>Free Roulette після 1 години, Advanced Roulette після 3 годин ігрового часу на сервері.</p>
                <a class="btn btn-sm btn-primary" href="roulette.php">Деталі рулетки</a>
            </article>
        </div>
    </section>

    <section class="portal-card mb-4">
        <h2 class="section-title">Чому саме [UA] Народний Паблік</h2>
        <div class="feature-grid">
            <div><strong>Чесна гра</strong><p>Стабільний сервер із фокусом на комфортний публічний геймплей.</p></div>
            <div><strong>Кастомізація</strong><p>Зручний SkinChanger із підтримкою knife/wear/seed налаштувань.</p></div>
            <div><strong>Спільнота</strong><p>Активні гравці, дружня атмосфера та швидкий старт без зайвих бар'єрів.</p></div>
            <div><strong>Розвиток</strong><p>Портал підготовлений до розширень: плагінна інтеграція, аналітика, адмін-панель.</p></div>
        </div>
    </section>
</main>

<?php render_footer(); ?>
