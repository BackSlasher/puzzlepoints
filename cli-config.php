<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

// Obtain EntityManager
$entityManager = require_once 'bootstrap.php';

ConsoleRunner::run(
    new SingleManagerProvider($entityManager)
);