<?php

namespace App\Services;

use App\Repositories\InstitutionRepository;

class InstitutionService
{
    public function __construct(protected InstitutionRepository $repo) {}

    public function getAllForDatatable()
    {
        return $this->repo->getAllForDatatable();
    }

    public function getTypes()
    {
        return $this->repo->getDistinctTypes();
    }

    public function getById($id)
    {
        return $this->repo->findById($id);
    }

    public function store(array $data)
    {
        $institution = $this->repo->store($data);
        return [
            'status' => (bool) $institution,
            'message' => $institution ? 'Institusi berhasil ditambahkan' : 'Gagal menambah institusi'
        ];
    }

    public function update($id, array $data)
    {
        $institution = $this->repo->update($id, $data);
        return [
            'status' => (bool) $institution,
            'message' => $institution ? 'Institusi berhasil diperbarui' : 'Gagal memperbarui institusi'
        ];
    }

    public function delete($id)
    {
        $deleted = $this->repo->delete($id);
        return [
            'status' => (bool) $deleted,
            'message' => $deleted ? 'Institusi berhasil dihapus' : 'Institusi tidak ditemukan'
        ];
    }
}
