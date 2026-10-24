# TugasKu

Aplikasi web sederhana untuk mencatat dan mengelola tugas pribadi: tambah, ubah, hapus,
cari, filter, kelompokkan per kategori, dan ubah status tanpa memuat ulang halaman (AJAX).
Tampilan menyesuaikan layar HP dan komputer.

## Teknologi

| Bagian | Yang dipakai |
|---|---|
| Bahasa | PHP 8.3, JavaScript, HTML, CSS |
| Framework | Laravel 12 |
| Login | Laravel Breeze (Blade) |
| Tampilan | Blade + Tailwind CSS |
| Interaksi | AJAX dengan `fetch` |
| Database | SQLite (bisa diganti MySQL) |
| Build aset | Vite + npm |
| Pengujian | PHPUnit |

## Cara menjalankan

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`, lalu masuk dengan akun contoh:

- Email: `demo@tugasku.test`
- Kata sandi: `password`

## Pengujian

```bash
php artisan test
```

## Struktur penting

```
app/Enums/TaskStatus.php        status tugas (belum, sedang, selesai)
app/Enums/TaskPriority.php      prioritas (rendah, sedang, tinggi)
app/Models/Task.php             model tugas + filter pencarian
app/Models/Category.php         model kategori
app/Http/Controllers/           TaskController, CategoryController, DashboardController
app/Http/Requests/TaskRequest   validasi form tugas
app/Policies/                   aturan izin (hanya pemilik)
database/migrations/            pembuatan tabel categories dan tasks
resources/views/components/     komponen yang dipakai ulang (task-card, badge)
resources/views/tasks/          halaman daftar, tambah, ubah tugas
resources/js/app.js             AJAX tombol ubah status
tests/Feature/                  test otomatis
```
