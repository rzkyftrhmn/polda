# Laravel Constitution --- Service & Repository Pattern

## 1. Purpose (Tujuan)

Dokumen ini menjadi dasar arsitektur proyek Laravel agar pengembangan
lebih rapi, terstruktur, dan mudah di-maintain melalui penggunaan
**Service--Repository Pattern** dengan penerapan **Single Responsibility
Principle (SRP)**.

## 2. Core Principles (Prinsip Utama)

### 1. Clean Separation of Concerns

-   Controller hanya menangani HTTP request/response.
-   Repository hanya menangani akses data.
-   Service hanya menangani logic bisnis.

### 2. Single Responsibility

-   Setiap method pada service hanya melakukan satu tugas yang jelas.
-   Setiap repository method fokus hanya pada operasi database tertentu.
-   Controller tidak boleh memiliki logic bisnis.

### 3. Dependency Injection

-   Semua service dan repository wajib di-inject.
-   Gunakan interface pada repository.

### 4. Reusability & Scalability

-   Logic berulang wajib dipindah ke service.
-   Penambahan fitur tidak boleh merusak arsitektur.

## 3. System Structure

    app/
     ├─ Http/
     │   └─ Controllers/
     ├─ Services/
     │   └─ *Service.php
     ├─ Repositories/
     │   ├─ Contracts/
     │   └─ Eloquent/
     └─ Models/

## 4. Roles & Responsibilities

### Controller

-   Menerima request & menghasilkan response.
-   Validasi ringan.
-   Tidak boleh ada logic bisnis atau query.

### Service

-   Semua business logic ditempatkan di sini.
-   Satu service = satu domain.
-   Setiap function wajib single responsibility.

### Repository

-   Bertugas hanya untuk CRUD dan operasi database.
-   Tidak mengandung business logic.

## 5. Process Flow

**Controller → Service → Repository → Model → Database**

## 6. Naming Rules

### Service

-   Contoh nama: `UserService`, `OrderService`.
-   Contoh method: `createUser()`, `updatePassword()`.

### Repository

-   Interface: `UserRepositoryInterface`
-   Implementasi: `UserRepository`
-   Contoh method: `findById()`, `create()`, `update()`.

## 7. Single Responsibility Enforcement

### Dilarang

-   Function dengan lebih dari satu tugas.
-   Controller berisi query.
-   Repository mengandung logic bisnis.
-   Service mengakses DB tanpa repository.

### Wajib
-   Token gunakan JWT
-   Relasi data gunakan model
-   Mapping data di model
-   Satu function = satu tujuan.

### Urutan Cara Baca Speckit
- specify.md
- task.md