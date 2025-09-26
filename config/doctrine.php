<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load local environment if it exists (for development)
if (file_exists(__DIR__ . '/../.env.local')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..', '.env.local');
    $dotenv->load();
}

// Database connection parameters from .env
if (isset($_ENV['DB_DRIVER']) && $_ENV['DB_DRIVER'] === 'sqlite') {
    // SQLite configuration for development
    $dbPath = $_ENV['DB_PATH'] ?? 'var/data.db';

    // Ensure directory exists
    $dbDir = dirname(__DIR__ . '/' . $dbPath);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }

    $connectionParams = [
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../' . $dbPath,
    ];
} else {
    // MySQL configuration for production
    $connectionParams = [
        'dbname' => $_ENV['DB_NAME'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
        'host' => $_ENV['DB_HOST'],
        'driver' => 'pdo_mysql',
        'charset' => 'utf8mb4',
    ];
}

// Entity paths
$paths = [__DIR__ . '/../src/Entity'];

// Development mode (use array cache for development, file cache for production)
$isDevMode = true;

// Cache configuration
$cache = $isDevMode ? new ArrayAdapter() : new FilesystemAdapter('', 0, __DIR__ . '/../var/cache');

// ORM configuration
$config = ORMSetup::createAttributeMetadataConfiguration(
    $paths,
    $isDevMode,
    null,
    $cache
);

// Create connection and EntityManager
$connection = DriverManager::getConnection($connectionParams);
$entityManager = new EntityManager($connection, $config);

return $entityManager;