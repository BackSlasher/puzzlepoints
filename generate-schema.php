<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Use a dummy connection for schema generation
$connectionParams = [
    'driver' => 'pdo_mysql',
    'memory' => true,
];

// Entity paths
$paths = [__DIR__ . '/src/Entity'];

// Development mode
$isDevMode = true;

// Cache configuration
$cache = new ArrayAdapter();

// ORM configuration
$config = ORMSetup::createAttributeMetadataConfiguration(
    $paths,
    $isDevMode,
    null,
    $cache
);

// Create a dummy connection for schema generation
$connection = DriverManager::getConnection($connectionParams);
$entityManager = new EntityManager($connection, $config);

// Get all metadata
$metadatas = $entityManager->getMetadataFactory()->getAllMetadata();

// Create schema tool
$schemaTool = new SchemaTool($entityManager);

// Generate SQL
$sqls = $schemaTool->getCreateSchemaSql($metadatas);

echo "Generated SQL Schema:\n";
echo "===================\n\n";

foreach ($sqls as $sql) {
    echo $sql . ";\n\n";
}