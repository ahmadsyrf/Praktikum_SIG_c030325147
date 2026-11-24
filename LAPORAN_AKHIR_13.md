# LAPORAN AKHIR PRAKTIKUM SISTEM INFORMASI GEOGRAFIS
**Topik: Supabase dan Deploy ke Vercel (Memindahkan Database dan Mengonlinekan Aplikasi)**  
**Nama**: Ahmad Syarif  
**NIM**: C030325147  
**Kelas**: TI3e-Axioo  
**Institusi**: Politeknik Negeri Banjarmasin - Jurusan Teknik Elektro - D3 Teknik Informatika (2026)  

---

## A. Ringkasan & Catatan Lingkungan Database
Pada praktikum-praktikum sebelumnya (Praktikum 1 - 27), aplikasi SIG Sekolah menggunakan database **MySQL lokal** (dijalankan via service MySQL lokal / XAMPP). Pada praktikum lanjutan ini, seluruh database dimigrasikan ke **PostgreSQL Supabase (Cloud)** dan aplikasi dideploy ke **Vercel** menggunakan Container Runtime (**FrankenPHP**).

---

## B. Skenario Pengujian Online dan Tindakan Keamanan

### 1. Tabel Hasil Pengujian Online
| No | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Status |
|:--:|:-------------------|:------------------|:----------------------|:------:|
| 1 | HTTPS | Buka alamat Vercel https://[project].vercel.app | Ikon gembok aman pada browser, tidak ada peringatan mixed content | Berhasil |
| 2 | Status Aplikasi | Akses endpoint `/up` | Menampilkan response HTTP 200 OK | Berhasil |
| 3 | Peta Publik | Buka 7 menu peta (Peta Sekolah, Pencarian, Klaster, Peta Panas, Wilayah, Rute, Cek Alamat) | Semua marker, klaster, dan poligon tampil lancar tanpa galat | Berhasil |
| 4 | Pencarian Case-Insensitive | Cari 'demo' (huruf kecil) dan 'DEMO' (huruf besar) | Menghasilkan data sekolah yang sama persis (berkat `whereLike()`) | Berhasil |
| 5 | Login Role-Based | Masuk menggunakan akun admin baru dengan kata sandi kuat | Berhasil masuk ke dashboard dengan menu sesuai hak akses admin | Berhasil |
| 6 | Sesi Persisten | Refresh halaman dan buka tab baru | Pengguna tetap login (sesi disimpan di tabel `sessions` Supabase) | Berhasil |
| 7 | Hak Akses (RBAC) | Akun role sekolah membuka halaman sekolah lain | Aplikasi menolak akses dan memunculkan HTTP 403 Forbidden | Berhasil |
| 8 | Operasi CRUD Peta | Dinas menambah sekolah baru dengan klik titik pada peta | Marker baru langsung muncul di peta publik dan tersimpan di Supabase | Berhasil |
| 9 | Throttle Login | Percobaan gagal login 6 kali berturut-turut | Muncul proteksi HTTP 429 Too Many Requests | Berhasil |
| 10 | Logout | Klik Keluar lalu coba akses kembali `/dashboard` | Sesi terhapus dan pengguna langsung dialihkan ke `/login` | Berhasil |
| 11 | Uji Cold Start | Buka web setelah beberapa menit sepi | Container Vercel menyala kembali (responsif dan normal) | Berhasil |
| 12 | Pembaruan Otomatis | Lakukan commit kecil dan push ke GitHub | Vercel otomatis membangun dan menerbitkan versi deployment baru | Berhasil |

### 2. Tindakan Pengamanan Sebelum Dibuka untuk Umum
1. **Menghapus Akun Demo Default**: Akun demo awal dengan password bawaan dihapus lewat `php artisan tinker`, lalu dibuat akun Admin resmi dengan kata sandi kuat.
2. **Mengaktifkan Row Level Security (RLS) di Supabase**: RLS diaktifkan pada semua tabel skema `public` tanpa kebijakan publik, sehingga akses via API publik Supabase ditolak dan database hanya bisa diakses oleh Laravel melalui role `postgres`.
3. **Mematikan Debug Mode**: Mengatur `APP_DEBUG=false` pada environment variable produksi di Vercel agar rincian error internal tidak bocor ke publik.
4. **Isolasi Berkas Rahasia (.env)**: Memastikan `.env` terdaftar di `.gitignore` dan `.dockerignore` sehingga tidak pernah ikut ter-push ke GitHub maupun masuk ke image Docker.
5. **Konfigurasi Trust Proxies**: Menambahkan `$middleware->trustProxies(at: '*')` pada `bootstrap/app.php` agar protokol HTTPS dari load balancer Vercel dikenali dengan benar.

---

## C. Pembahasan dan Analisis Teori

### 1. Perbedaan Session Mode (Port 5432) dan Transaction Mode (Port 6543)
- **Session Mode (Port 5432)**: Pooler mempertahankan koneksi tetap aktif sepanjang sesi klien tersambung. Mode ini mendukung prepared statements di sisi server secara native. Sangat cocok digunakan untuk pengembangan lokal, eksekusi migrasi (`php artisan migrate`), dan seeder.
- **Transaction Mode (Port 6543)**: Pooler meminjamkan koneksi hanya selama satu transaksi query berlangsung lalu segera mengembalikannya ke pool. Mode ini sangat ideal untuk lingkungan serverless (seperti Vercel) yang membuka ribuan koneksi singkat secara efisien. Namun mode ini tidak mendukung prepared statement server-side, sehingga Laravel wajib menyetel `PDO::ATTR_EMULATE_PREPARES => true` (`DB_EMULATE_PREPARES=true`).

### 2. Alasan Mengapa Aplikasi Harus Bersifat Stateless di Vercel
Container runtime di Vercel bersifat ephemeral (sementara): instance dapat dibuat, dimatikan saat idle, atau diskalakan ke berbagai server kapan saja. Disk lokal di container tidak permanen dan tidak dibagikan antar instance. Jika sesi atau cache disimpan di disk lokal container, pengguna akan sering ter-logout saat berpindah instance. Oleh karena itu, aplikasi harus stateless, di mana seluruh state (sesi dan cache) disimpan di layanan terpusat eksternal, yaitu tabel `sessions` dan `cache` di PostgreSQL Supabase.

### 3. Penjelasan Multi-Stage Build Dockerfile.vercel
- **Tahap 1 (assets)**: Menggunakan `node:22-alpine` untuk mengompilasi aset frontend Vite & Tailwind CSS ke direktori `public/build`.
- **Tahap 2 (base)**: Menyiapkan base image `dunglas/frankenphp:1-php8.4-bookworm` serta memasang ekstensi PHP `intl`, `zip`, `pdo_pgsql`.
- **Tahap 3 (dependencies)**: Menggunakan Composer untuk menginstal pustaka PHP dengan optimasi produksi `--no-dev` dan classmap authoritative.
- **Tahap 4 (runtime)**: Menggabungkan aset, dependensi PHP, Caddyfile, menyetel permission `www-data`, dan menjalankan FrankenPHP pada port 80.

---

## D. Latihan Mandiri (Tugas Mandiri yang Paling Bawah - Poin 8)

### Tugas:
> **"Pasang pemeriksaan otomatis (GitHub Actions) yang menjalankan `php artisan test` sebelum deploy."**

### 1. Implementasi Alur Kerja (CI Workflow)
Dibuat berkas alur kerja GitHub Actions pada direktori `.github/workflows/tests.yml`:

```yaml
name: Laravel Tests (CI)

on:
  push:
    branches: [ master, main ]
  pull_request:
    branches: [ master, main ]
  workflow_dispatch:

jobs:
  laravel-tests:
    name: Run Automated Tests
    runs-on: ubuntu-latest

    steps:
      - name: Checkout Repository
        uses: actions/checkout@v4

      - name: Setup PHP Environment
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, ctype, iconv, intl, pdo, sqlite, pdo_sqlite
          coverage: none

      - name: Setup Node.js Environment
        uses: actions/setup-node@v4
        with:
          node-version: 22
          cache: 'npm'

      - name: Install Node Dependencies and Build Assets
        run: |
          npm ci
          npm run build

      - name: Copy .env.example to .env
        run: php -r "file_exists('.env') || copy('.env.example', '.env');"

      - name: Cache Composer Dependencies
        uses: actions/cache@v4
        with:
          path: vendor
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-composer-

      - name: Install Composer Dependencies
        run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

      - name: Generate Application Key
        run: php artisan key:generate

      - name: Set Directory Permissions
        run: chmod -R 777 storage bootstrap/cache

      - name: Execute Automated Tests (php artisan test)
        run: php artisan test
```

### 2. Penjelasan Tahapan Workflow:
- **Trigger**: Otomatis aktif saat ada `push` atau `pull_request` pada branch `master` dan `main`, serta dapat dijalankan manual (`workflow_dispatch`).
- **Setup Runner**: Menyiapkan runner `ubuntu-latest`, PHP 8.2 dengan ekstensi `pdo_sqlite` untuk database uji in-memory, dan Node.js 22.
- **Build Frontend**: Menginstal dependensi NPM dan melakukan `npm run build` sehingga aset terverifikasi bebas error.
- **Install & Cache Dependencies**: Menggunakan cache composer untuk mempercepat proses instalasi dependensi Laravel.
- **Eksekusi Pengujian**: Menjalankan `php artisan test` yang mengeksekusi 15 unit dan feature tests secara otomatis.

### 3. Hasil Pengujian Otomatis (100% PASS):
```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                                            0.03s  

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                                                0.77s  
  ✓ map tool pages use the full screen map layout                                                                0.10s  

   PASS  Tests\Feature\RbacTest
  ✓ tamu dialihkan ke login                                                                                      0.45s  
  ✓ login gagal dan berhasil                                                                                     0.47s  
  ✓ login dapat mengingat pengguna                                                                               0.06s  
  ✓ admin hanya mengelola pengguna                                                                               0.12s  
  ✓ dashboard render untuk dinas dan operator                                                                    0.07s  
  ✓ admin menambah pengguna sekolah                                                                              0.08s  
  ✓ role sekolah wajib memilih sekolah                                                                           0.05s  
  ✓ dinas mengelola semua sekolah                                                                                0.09s  
  ✓ sekolah hanya mengakses sekolah sendiri                                                                      0.07s  
  ✓ sekolah tidak dapat mengubah npsn                                                                            0.08s  
  ✓ sekolah menyimpan profil sendiri                                                                             0.07s  
  ✓ sekolah tidak dapat menghapus sekolah                                                                        0.06s  

  Tests:    15 passed (61 assertions)
  Duration: 3.14s
```

### 4. Status Integrasi GitHub
Commit telah berhasil dipush ke repositori:
`https://github.com/ahmadsyrf/Praktikum_SIG_c030325147.git` (Branch `master`).
