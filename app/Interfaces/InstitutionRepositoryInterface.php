<?php

namespace App\Interfaces;

interface InstitutionRepositoryInterface
{
    /**
     * Return all institutions ordered by name.
     */
    public function getAllOrderedByName();
}