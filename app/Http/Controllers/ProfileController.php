<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $service)
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = auth()->user()->load('institution', 'division', 'roles');

        return view('pages.profile.show', [
            'title' => 'Profile',
            'user' => $user,
        ]);
    }

    public function edit()
    {
        $user = auth()->user()->load('institution', 'division', 'roles');

        return view('pages.profile.edit', [
            'title' => 'Profile',
            'user' => $user,
            'institutions' => $this->service->getInstitutions(),
            'divisions' => $this->service->getDivisions(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'institution_id' => ['required', 'integer', 'exists:institutions,id'],
            'division_id' => ['required', 'integer', 'exists:divisions,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        DB::beginTransaction();
        try {
            $this->service->updateProfile($user->id, $data, $request->file('photo'));
            DB::commit();
            return back()->with('success', 'Profil berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui profil.');
        }
    }

    public function deletePhoto(Request $request)
    {
        $user = $request->user();
        DB::beginTransaction();
        try {
            $this->service->deletePhoto($user->id);
            DB::commit();
            return back()->with('success', 'Foto profil berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Gagal menghapus foto profil.');
        }
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        DB::beginTransaction();
        try {
            $this->service->updatePassword($request->user()->id, $data['password']);
            DB::commit();
            return back()->with('success', 'Password berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Gagal mengubah password.');
        }
    }
}
