# Panduan Memahami Kode TugasKu

Panduan singkat agar setiap bagian kode bisa dijelaskan dengan bahasa sendiri.

## 1. Gambaran besar

TugasKu memakai pola **MVC** milik Laravel:

- **Model** (`app/Models`) = mewakili tabel database.
- **View** (`resources/views`) = tampilan HTML (Blade).
- **Controller** (`app/Http/Controllers`) = penghubung: menerima permintaan, mengambil/menyimpan data lewat model, lalu menampilkan view.

Alur saat pengguna membuka halaman Tugas:

```
Browser → routes/web.php → TaskController@index → Model Task (ambil data) → view tasks/index → Browser
```

## 2. Database (ERD)

Tiga tabel:

```
users (1) ──< (banyak) categories
users (1) ──< (banyak) tasks
categories (1) ──< (banyak) tasks      ← kategori boleh kosong
```

- `tasks.user_id` → pemilik tugas (wajib).
- `tasks.category_id` → kategori (boleh kosong). Jika kategori dihapus, tugas tetap ada dan kategorinya menjadi kosong (`nullOnDelete`).
- `categories` punya aturan unik `(user_id, name)`: satu pengguna tidak bisa membuat dua kategori bernama sama.

File: `database/migrations/*_create_categories_table.php`, `*_create_tasks_table.php`.

## 3. Struktur data

| Struktur data | Di mana | Gunanya |
|---|---|---|
| **Enum** | `app/Enums/TaskStatus.php`, `TaskPriority.php` | Nilai status/prioritas hanya boleh dari daftar tetap; label dan warna ditulis di satu tempat. |
| **Array asosiatif** | `DashboardController` (`$counts['todo']`) | Menyimpan jumlah tugas per status. |
| **Collection** | hasil query, mis. `$categories` | Kumpulan data yang bisa diulang di view (`@foreach`). |
| **JSON** | jawaban `updateStatus` | Data yang dikirim ke JavaScript saat AJAX. |

`casts()` di model `Task` mengubah isi kolom `status` (teks) menjadi objek enum secara otomatis.

## 4. File-file kunci

| File | Isi singkat |
|---|---|
| `routes/web.php` | Daftar alamat halaman. Semua halaman tugas dibungkus `middleware('auth')` = harus login. |
| `app/Http/Controllers/TaskController.php` | `index` (daftar + filter), `create`/`store` (tambah), `edit`/`update` (ubah), `destroy` (hapus), `updateStatus` (AJAX). |
| `app/Http/Requests/TaskRequest.php` | Aturan validasi: judul wajib, status/prioritas harus nilai enum, kategori harus milik pengguna. |
| `app/Policies/TaskPolicy.php` | Hanya pemilik yang boleh mengubah/menghapus. Dipanggil dengan `Gate::authorize('update', $task)`. |
| `app/Models/Task.php` | Relasi (`user`, `category`), `scopeFilter` untuk pencarian, `isOverdue()` untuk cek terlambat. |
| `resources/views/components/task-card.blade.php` | Kartu tugas, dipakai ulang di Dashboard dan halaman Tugas. |
| `resources/js/app.js` | AJAX tombol Mulai/Selesai. |

## 5. AJAX: tombol Mulai / Selesai

1. Tombol punya atribut `data-next-status="/tasks/5/status"`.
2. Saat diklik, `app.js` mengirim `fetch(..., { method: 'PATCH' })` beserta token CSRF.
3. Server (`TaskController@updateStatus`) mengecek izin, memindahkan status satu langkah (`TaskStatus::next()`), lalu menjawab JSON: `{ status, label, color, done }`.
4. JavaScript mengganti teks & warna badge tanpa memuat ulang halaman.

Token **CSRF** = kode rahasia per sesi supaya permintaan hanya diterima dari halaman aplikasi sendiri.

## 6. Bagian yang dipakai ulang (reusable)

- Komponen Blade: `x-task-card`, `x-status-badge`, `x-priority-badge`, `x-flash`.
- Partial `tasks/_form.blade.php`: satu form untuk tambah **dan** ubah.
- Method enum `label()`, `color()`, `next()`.
- `scopeFilter()` di model Task.
- `formData()` di TaskController: data form yang sama untuk create & edit.

## 7. Keamanan sederhana

- Harus login (`auth`).
- Hanya pemilik yang boleh mengubah/menghapus (Policy) → orang lain mendapat **403 Forbidden**.
- Validasi input (`TaskRequest`).
- Token CSRF di setiap form (`@csrf`).
- Kata sandi disimpan dalam bentuk hash (bawaan Laravel).

## 8. Perintah yang sering dipakai

| Perintah | Fungsi |
|---|---|
| `composer install` | Memasang library PHP (folder `vendor`). |
| `npm install` | Memasang paket JavaScript (folder `node_modules`). |
| `php artisan key:generate` | Membuat `APP_KEY` untuk enkripsi. |
| `php artisan migrate --seed` | Membuat tabel dan mengisi data contoh. |
| `npm run build` | Menyusun CSS & JS ke `public/build`. |
| `php artisan serve` | Menjalankan server lokal. |
| `php artisan test` | Menjalankan test otomatis. |

## 9. Pertanyaan yang mungkin diajukan

- **Kenapa Laravel?** Sudah menyediakan login, validasi, migration, dan pengujian, jadi lebih cepat dan aman.
- **Apa itu migration?** File PHP yang membuat/mengubah tabel, sehingga struktur database tercatat dan bisa diulang.
- **Apa beda `PUT` dan `PATCH`?** `PUT` mengganti data tugas lewat form; `PATCH` mengubah sebagian, di sini hanya status.
- **Bagaimana kalau orang lain mengubah tugas saya lewat URL?** Ditolak Policy dengan 403.
- **Kenapa kategori boleh kosong?** Tidak semua tugas perlu dikelompokkan; kalau kategori dihapus, tugasnya tetap aman.
