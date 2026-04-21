<?php

class SafeDatabase
{
    private ?PDO $pdo = null;
    private ?string $connectionError = null;

    public function __construct()
    {
        if (!defined('DB_HOST') || !defined('DB_PORT') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
            $this->connectionError = 'Database constants are not defined.';
            return;
        }

        if (DB_HOST === '' || DB_PORT === '' || DB_NAME === '' || DB_USER === '') {
            $this->connectionError = 'Database credentials are not configured.';
            return;
        }

        try {
            $this->pdo = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (Throwable $exception) {
            $this->connectionError = 'Database connection failed.';
        }
    }

    public function isConnected(): bool
    {
        return $this->pdo instanceof PDO;
    }

    public function getConnectionError(): ?string
    {
        return $this->connectionError;
    }

    public function select(string $query, array $bindings = []): array
    {
        if (!$this->isConnected()) {
            return [];
        }

        $statement = $this->pdo->prepare($query);
        $statement->execute($bindings);
        return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function query(string $query, array $bindings = []): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        $statement = $this->pdo->prepare($query);
        return $statement->execute($bindings);
    }
}
