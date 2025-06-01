<?php
require_once __DIR__ . '/Database.php';
use helpers\Database;

$schema = file_get_contents(__DIR__ . '/../schema.sql');

try {
    $pdo = Database::getInstance();
    $pdo->exec($schema);
    echo "Migration successful.";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage();
}
