<?php

function detect_country_code(): string
{
    $headersToCheck = ['HTTP_CF_IPCOUNTRY', 'GEOIP_COUNTRY_CODE'];
    foreach ($headersToCheck as $header) {
        if (!empty($_SERVER[$header]) && preg_match('/^[A-Z]{2}$/', $_SERVER[$header])) {
            return $_SERVER[$header];
        }
    }

    return 'UN';
}

function client_ip_hash(): string
{
    $rawIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $salt = 'ua_narodnyi_public_portal_salt';

    return hash('sha256', $salt . $rawIp);
}

function tracking_pdo(): ?PDO
{
    if (!defined('DB_HOST') || !defined('DB_PORT') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
        return null;
    }

    if (DB_HOST === '' || DB_PORT === '' || DB_NAME === '' || DB_USER === '') {
        return null;
    }

    try {
        return new PDO(
            'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME,
            DB_USER,
            DB_PASS,
            [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (Throwable $exception) {
        return null;
    }
}

function track_visit(string $route): void
{
    static $alreadyTracked = false;
    if ($alreadyTracked) {
        return;
    }
    $alreadyTracked = true;

    $pdo = tracking_pdo();
    if ($pdo === null) {
        return;
    }

    try {
        $pdo->exec('CREATE TABLE IF NOT EXISTS website_visits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            route VARCHAR(255) NOT NULL,
            country_code CHAR(2) NOT NULL DEFAULT "UN",
            ip_hash CHAR(64) NOT NULL,
            user_agent VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_route_created(route, created_at),
            INDEX idx_country_created(country_code, created_at)
        )');

        $statement = $pdo->prepare('INSERT INTO website_visits (route, country_code, ip_hash, user_agent) VALUES (:route, :country, :ip_hash, :user_agent)');
        $statement->execute([
            'route' => substr($route, 0, 255),
            'country' => detect_country_code(),
            'ip_hash' => client_ip_hash(),
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255),
        ]);
    } catch (Throwable $exception) {
        // Ignore tracking failures to keep public pages always available.
    }
}
