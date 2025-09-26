<?php

require_once __DIR__ . '/vendor/autoload.php';

echo "PuzzlePoints Database Setup\n";
echo "===========================\n\n";

try {
    // Get EntityManager
    $entityManager = require_once __DIR__ . '/bootstrap.php';

    // Test database connection
    echo "Testing database connection...\n";
    $connection = $entityManager->getConnection();
    $connection->executeQuery('SELECT 1');
    echo "✓ Database connection successful\n\n";

    // Get all metadata
    $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();

    if (empty($metadatas)) {
        echo "No entities found!\n";
        exit(1);
    }

    echo "Found entities:\n";
    foreach ($metadatas as $metadata) {
        echo "  - " . $metadata->getName() . "\n";
    }
    echo "\n";

    // Create schema tool
    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);

    // Check if tables already exist
    echo "Checking existing schema...\n";
    $existingSql = $schemaTool->getUpdateSchemaSql($metadatas, true);
    if (empty($existingSql)) {
        echo "✓ Schema is up to date\n";
    } else {
        echo "Schema needs updates:\n";
        foreach ($existingSql as $sql) {
            echo "  - " . substr($sql, 0, 50) . "...\n";
        }
        echo "\nUpdating schema...\n";
        $schemaTool->updateSchema($metadatas, true);
        echo "✓ Schema updated successfully\n";
    }

    echo "\n✓ Database setup complete!\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Make sure your .env file has the correct database credentials.\n";
    exit(1);
}