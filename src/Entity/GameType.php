<?php

namespace App\Entity;

enum GameType: string
{
    case WORDLE = 'wordle';
    case CONNECTIONS = 'connections';
    case STRANDS = 'strands';
    case MINI_CROSSWORD = 'mini_crossword';
    case SPELLING_BEE = 'spelling_bee';
    case OTHER = 'other';
}