# Users Feature – Service–Repository Pattern Journey

Tujuan dokumen ini adalah mengajarkan alur (journey) pengembangan fitur Users dengan arsitektur Service–Repository di proyek ini. Dokumen dibuat praktis, terstruktur, dan mudah diikuti oleh junior engineer.

## Ringkasan Arsitektur

- Controller: Menangani HTTP request/response, validasi, dan mengatur flow (transaksi, redirect, JSON).
- Service: Mengimplementasikan logika bisnis/aturan aplikasi, mengorkestrasi beberapa repository, dan menangani detail non‑UI (mis. hashing password, sinkronisasi role).
- Repository: Abstraksi akses data ke database via Eloquent (query, join, CRUD dasar).
- Model: Representasi entitas (User) beserta relasi ke Institution dan Division, serta casts/mutators.
- View (Blade): UI untuk Create/Edit/Show, memanfaatkan komponen dan plugin (Select2, DataTables, SweetAlert2).
- JS: Inisialisasi DataTables/Select2, SweetAlert2 untuk konfirmasi dan flash messages.

## Struktur File Terkait

- app/Http/Controllers/UserController.php
- app/Services/UserService.php
- app/Repositories/UserRepository.php
- app/Models/User.php
- resources/views/pages/users/create.blade.php
- resources/views/pages/users/show.blade.php
- resources/views/components/script.blade.php
- resources/views/components/style.blade.php
- routes/web.php (resource routes)

## Alur Lengkap Fitur Users

### 1) Listing + DataTables (Server-side)

1. Client memuat halaman Users (GET /users) → `UserController@index`
2. Tabel diinisialisasi DataTables (client-side) dan melakukan AJAX ke endpoint datatables (*misal* route `users.datatables`) → `UserController@datatables`
3. Controller meminta data join dari service → service meminta base query dari repository:

   - Repository menyusun query dengan left join institutions & divisions dan select kolom turunan:
     - File: `app/Repositories/UserRepository.php`
     - Method: `getAllForDatatable()`

4. Controller menerapkan filter tambahan (filter_q, institution_id, division_id, global search), memastikan kolom yang ambigu diprefix dengan tabel (contoh: `users.name`).
5. Controller menghitung total, mengurutkan (jika kolom orderable), melakukan pagination (skip/take), dan mengembalikan JSON ke DataTables.
6. Action buttons pada setiap baris:
   - Detail (link ke `users.show`)
   - Edit (link ke `users.edit`)
   - Delete (SweetAlert2 konfirmasi → AJAX DELETE ke `users.destroy`)

Catatan penting: gunakan prefix tabel untuk kolom yang juga ada di tabel join (mis. `users.name`) agar terhindar dari error “Column 'name' is ambiguous”.

### 2) Create User

1. GET `/users/create` → `UserController@create`
   - Service menyediakan master data: institutions, divisions, roles → untuk dropdown di view.
   - View yang dipakai: `resources/views/pages/users/create.blade.php` (reusable untuk create & edit).

2. POST `/users` → `UserController@store`
   - Validasi request (required/unique/exists, password confirmed).
   - Buka transaksi DB (`DB::beginTransaction()`).
   - Panggil `UserService@store($validated)`:
     - Jika ada `password`, service melakukan `Hash::make()` sebelum memanggil repository.
     - Repository membuat user via `User::create($payload)`.
     - Service assign role ke user jika diberikan (`$user->assignRole($data['role'])`).
   - Jika sukses, `DB::commit()` → redirect ke index dengan `session('success')`.
   - Jika gagal/exception, `DB::rollBack()` → kembali ke form dengan `session('error')` dan `old input`.
   - SweetAlert2 menampilkan flash success/error (lihat bagian JS).

### 3) Edit & Update User

1. GET `/users/{id}/edit` → `UserController@edit`
   - Ambil user via service.
   - Ambil master data (institutions, divisions, roles) dan role saat ini (`$user->roles()->pluck('name')->first()`).
   - Render view yang sama dengan create (`pages.users.create`) namun mengisi value awal (pre-fill) dan mengganti method menjadi PUT.

2. PUT `/users/{id}` → `UserController@update`
   - Validasi dengan rules unique yang mengabaikan ID saat ini (`Rule::unique(...)->ignore($id)`).
   - Service melakukan hashing password hanya jika field password diisi; jika kosong, tidak menimpa password lama.
   - Repository menjalankan update, service `syncRoles([$data['role']])` jika role disediakan.
   - Transaksi DB commit/rollback sama seperti create.
   - Flash message (success/error) ditangani oleh SweetAlert.

### 4) Show User (Detail)

1. GET `/users/{id}` → `UserController@show`
   - Ambil user via service, jika tidak ada redirect dengan error.
   - Load relasi untuk ditampilkan (`institution`, `division`, `roles`).
   - Kirim ke view `resources/views/pages/users/show.blade.php` yang menampilkan field dasar dan role names.

### 5) Delete User (SweetAlert2 + AJAX)

1. Klik tombol delete pada tabel → Script menampilkan SweetAlert konfirmasi.
2. Jika konfirmasi → kirim AJAX DELETE ke route `users.destroy` dengan `_token` & `_method` (DELETE).
3. Controller `destroy` mencegah self‑delete (user tidak bisa menghapus dirinya sendiri), lalu memanggil service `delete`, repository `delete($id)`, commit/rollback.
4. Controller mengembalikan JSON `{status, message}`.
5. Script menampilkan SweetAlert sukses/gagal dan reload DataTable (atau refresh halaman).

## Implementasi Tiap Lapisan

### Controller (app/Http/Controllers/UserController.php)

- Tanggung jawab:
  - Validasi request
  - Orkestrasi transaksi DB
  - Memanggil service
  - Menentukan response (JSON atau redirect)
  - Menangani keamanan sederhana (self‑delete protection)

Contoh cuplikan penting:

```php
// store
$validated = $request->validate([
    'name' => ['required','string','max:255'],
    'email' => ['required','email','max:255','unique:users,email'],
    'password' => ['required','string','min:6','confirmed'],
    'username' => ['nullable','string','max:255','unique:users,username'],
    'institution_id' => ['nullable','integer','exists:institutions,id'],
    'division_id' => ['nullable','integer','exists:divisions,id'],
    'role' => ['required','string','exists:roles,name'],
]);

DB::beginTransaction();
try {
    $result = $this->service->store($validated);
    if (!empty($result['status'])) {
        DB::commit();
        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }
    DB::rollBack();
    return back()->withInput()->with('error', $result['message'] ?? 'Terjadi kesalahan saat membuat user.');
} catch (\Throwable $e) {
    DB::rollBack();
    return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat user.');
}
```

```php
// datatables – filter_q dan global search gunakan prefix tabel users.
$posts = $posts->where(function ($q) use ($filterQ) {
    $q->where('users.username', 'like', "%$filterQ%")
      ->orWhere('users.name', 'like', "%$filterQ%")
      ->orWhere('users.email', 'like', "%$filterQ%");
});
```

### Service (app/Services/UserService.php)

- Tanggung jawab:
  - Mengorkestrasi beberapa repository (User, Institution, Division, Role)
  - Logika bisnis (hashing password, assign/sync role)
  - Mengembalikan struktur hasil yang konsisten untuk controller

Cuplikan inti:

```php
public function store($data)
{
    if (isset($data['password']) && $data['password'] !== '') {
        $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
    }
    $user = $this->repo->store($data);
    if ($user && isset($data['role']) && !empty($data['role'])) {
        $user->assignRole($data['role']);
    }
    return ['status' => (bool) $user, 'message' => 'User created successfully', 'data' => $user];
}

public function update($id, $data)
{
    if (isset($data['password']) && $data['password'] !== '') {
        $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
    } else {
        unset($data['password']);
    }
    $updated = $this->repo->update($id, $data);
    $user = $this->repo->findById($id);
    if ($user && isset($data['role']) && !empty($data['role'])) {
        $user->syncRoles([$data['role']]);
    }
    return ['status' => (bool) $updated, 'message' => 'User updated successfully', 'data' => $user];
}
```

### Repository (app/Repositories/UserRepository.php)

- Tanggung jawab:
  - CRUD ke database melalui Eloquent
  - Menyediakan base query untuk DataTables dengan join/select kolom turunan

Cuplikan inti:

```php
public function getAllForDatatable()
{
    return (new User())
        ->leftJoin('institutions', 'institutions.id', '=', 'users.institution_id')
        ->leftJoin('divisions', 'divisions.id', '=', 'users.division_id')
        ->select('users.*', 'institutions.name as institution_name', 'divisions.name as division_name');
}

public function store($payload) { return (new User())->create($payload); }
public function findById($id) { return (new User())->find($id); }
public function update($id, $payload) { return (new User())->find($id)->update($payload); }
public function delete($id) { return (new User())->find($id)->delete(); }
```

### Model (app/Models/User.php)

- `fillable`: kolom yang bisa diisi mass assignment.
- `casts`: `password => 'hashed'` untuk auto hash saat set attribute.
- Relasi: `belongsTo(Institution)`, `belongsTo(Division)`.

### Views (Blade)

- `resources/views/pages/users/create.blade.php`
  - Satu file untuk create & edit. Menentukan action & method (POST/PUT) secara dinamis, pre-fill value jika edit, menampilkan dropdown Role/Institution/Division (Select2), dan tombol Simpan/Update.
  - Password required saat create, opsional saat edit.

- `resources/views/pages/users/show.blade.php`
  - Menampilkan detail user: name, email, username, institution, division, roles, created_at.
  - Tombol kembali & edit.

### JavaScript (components/script.blade.php)

- Inisialisasi Select2 untuk dropdown, menonaktifkan bootstrap‑select jika pernah terpasang agar tidak double UI.
- SweetAlert2:
  - Handler global untuk flash message `session('success')`, `session('error')`, dan `$errors->any()`.
  - Konfirmasi delete dengan AJAX DELETE, reload DataTables atau refresh halaman.

### Styling (components/style.blade.php)

- Perbaikan tema gelap Select2: memastikan placeholder dan teks ter‑center, tidak bertabrakan dengan arrow.

## Routing

Tambahkan resource route di `routes/web.php` (sesuaikan middleware auth):

```php
Route::middleware('auth')->group(function(){
    Route::resource('users', \App\Http\Controllers\UserController::class);
    // Endpoint DataTables jika dipisah
    Route::get('users/datatables', [\App\Http\Controllers\UserController::class, 'datatables'])->name('users.datatables');
});
```

## Validasi & Keamanan

- Validasi unik email/username saat create; saat update gunakan `Rule::unique()->ignore($id)`.
- Password minimal 6 dan `confirmed` (menggunakan `password_confirmation`).
- Hashing password dilakukan di service (dan didukung casts di model).
- Transaksi DB untuk operasi create/update/delete guna menjaga konsistensi data.
- Cegah self‑delete pada `destroy`.
- CSRF token wajib untuk POST/PUT/DELETE (AJAX menyertakan `_token`).

## Testing Manual

1. Create
   - Isi semua field, pastikan role wajib dipilih.
   - Submit → harus redirect ke index dengan SweetAlert sukses.
   - Cek database: kolom `password` tersimpan sebagai hash (`$2y$...`).

2. Edit
   - Buka edit user, ubah data tanpa mengisi password → password lama harus tetap.
   - Ubah dengan password baru → tersimpan hash baru.
   - Periksa unique email/username berfungsi.

3. Show
   - Buka halaman detail, pastikan relasi institution/division/roles tampil benar.

4. Delete
   - Klik delete, konfirmasi SweetAlert, cek user terhapus dan DataTables reload.
   - Coba hapus akun yang sedang login → harus ditolak dengan pesan error.

5. Filter & Search
   - Gunakan filter_q/global search → hasil sesuai, tanpa error kolom ambigu.

## Tips & Pitfall Umum

- Prefix kolom saat join beberapa tabel (contoh: `users.name`).
- Hindari menimpa password saat update jika field kosong.
- Sinkronisasi role: gunakan `assignRole` saat create dan `syncRoles` saat update.
- Pastikan komponen JS (Select2, SweetAlert2, DataTables) dimuat satu kali dan tidak bentrok (hindari bootstrap‑select jika Select2 dipakai).
- Gunakan transaksi untuk operasi tulis agar mudah rollback saat gagal.

## Glosarium Singkat

- Service–Repository: Pola arsitektur yang memisahkan logika bisnis (Service) dari akses data (Repository) untuk meningkatkan modularitas, testability, dan maintainability.
- DataTables: Plugin tabel interaktif dengan dukungan server‑side processing.
- Select2: Plugin untuk select yang lebih kaya fitur, dipakai di form create/edit.
- SweetAlert2: Popup/alert modern untuk notifikasi dan konfirmasi (delete).

---

Dengan mengikuti journey di atas, junior engineer dapat memahami bagaimana controller, service, repository, model, view, dan JS berkolaborasi untuk mewujudkan fitur Users yang bersih, aman, dan mudah dirawat.