<?php

use Doctrine\ORM\EntityManager;

class HomeController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): void
    {
        // Redirect to puzzles page
        header('Location: /puzzles');
        exit;
    }
}