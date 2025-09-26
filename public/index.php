<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Get EntityManager
$entityManager = require_once __DIR__ . '/../bootstrap.php';

// Simple routing
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string from request
$request = parse_url($request, PHP_URL_PATH);

// Routes
switch ($request) {
    case '/':
        require_once __DIR__ . '/../src/Controller/HomeController.php';
        $controller = new HomeController($entityManager);
        $controller->index();
        break;

    case '/input':
        require_once __DIR__ . '/../src/Controller/InputController.php';
        $controller = new InputController($entityManager);
        if ($method === 'POST') {
            $controller->submit();
        } else {
            $controller->show();
        }
        break;

    case '/results':
        require_once __DIR__ . '/../src/Controller/ResultsController.php';
        $controller = new ResultsController($entityManager);
        $controller->index();
        break;

    case '/logout':
        session_start();
        session_destroy();
        header('Location: /input');
        exit;

    default:
        // Check if it's a game-specific results page like /results/wordle/2025-01-15
        if (preg_match('/^\/results\/([^\/]+)\/([^\/]+)$/', $request, $matches)) {
            require_once __DIR__ . '/../src/Controller/ResultsController.php';
            $controller = new ResultsController($entityManager);
            $controller->gameResults($matches[1], $matches[2]);
        } else {
            http_response_code(404);
            echo "404 - Page not found";
        }
        break;
}