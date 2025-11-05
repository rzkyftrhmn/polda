<?php

namespace App\Services;

use App\Interfaces\DivisionRepositoryInterface;
use App\Interfaces\InstitutionRepositoryInterface;
use App\Interfaces\ProfileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function __construct(
        private ProfileRepositoryInterface $repo,
        private InstitutionRepositoryInterface $institutionRepo,
        private DivisionRepositoryInterface $divisionRepo
    ) {}

    public function getInstitutions()
    {
        return $this->institutionRepo->getAllOrderedByName();
    }

    public function getDivisions()
    {
        return $this->divisionRepo->getAllOrderedByName();
    }

    public function updateProfile($id, array $data, ?UploadedFile $photo = null)
    {
        $user = $this->repo->findById($id);
        $newPath = null;

        try {
            if ($photo) {
                $newPath = $photo->store('profile', 'public');
                $data['photo'] = $newPath;
            } else {
                unset($data['photo']);
            }

            $updatedUser = $this->repo->update($id, $data);

            if ($newPath && $user->photo && $user->photo !== $newPath) {
                Storage::disk('public')->delete($user->photo);
            }

            return $updatedUser;
        } catch (\Throwable $th) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $th;
        }
    }

    public function updatePassword($id, string $password)
    {
        return $this->repo->update($id, ['password' => Hash::make($password)]);
    }
}
