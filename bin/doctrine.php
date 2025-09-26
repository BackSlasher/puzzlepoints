<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

// Obtain EntityManager
$entityManager = require_once __DIR__ . '/../bootstrap.php';

ConsoleRunner::run(
    new SingleManagerProvider($entityManager)
);