<?php

namespace App\Interfaces;

interface DivisionRepositoryInterface
{
    /**
     * Return all divisions ordered by name.
     */
    public function getAllOrderedByName();
}