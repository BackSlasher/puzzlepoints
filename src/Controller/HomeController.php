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
        // Redirect to input page for now
        header('Location: /input');
        exit;
    }
}