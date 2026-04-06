# Nusantara Pangkas Rambut

Sistem manajemen barbershop multi-cabang berbasis web yang dibangun dengan Laravel 12, Filament v3, dan Spatie Permission + Filament Shield.

## Daftar Isi

- [Fitur](#fitur)
- [Tech Stack](#tech-stack)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
  - [Quick Setup](#quick-setup)
  - [Manual Setup](#manual-setup)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Default](#akun-default)
- [Struktur Database](#struktur-database)
- [Struktur Project](#struktur-project)
- [Manajemen Role & Permission](#manajemen-role--permission)
- [Testing](#testing)
- [Deployment ke Production](#deployment-ke-production)
- [Lisensi](#lisensi)

---

## Fitur

- **Multi-cabang (Branch):** Kelola beberapa cabang barbershop dalam satu sistem dengan isolasi data otomatis per cabang
- **Panel Admin Filament:** CRUD lengkap untuk cabang, karyawan, layanan, produk, transaksi, dan pelanggan (`/admin`)
- **POS Kasir:** Sistem point-of-sale berbasis Livewire untuk kasir (`/kasir/pos`)
- **Reservasi:** Pelanggan dapat booking appointment online dengan memilih layanan dan waktu
- **Portal Pelanggan:** Registrasi dan login terpisah untuk pelanggan (`/login`, `/register`)
- **Manajemen Karyawan:** Data karyawan terhubung dengan cabang dan akun user
- **Manajemen Layanan & Produk:** Daftar layanan dan produk beserta harga dan status aktif
- **Transaksi:** Pencatatan transaksi dengan nomor invoice otomatis (format `INV-YYYYMMDD-NNNN`), multi-item, dan kalkulasi subtotal otomatis
- **Dashboard Analitik:** Grafik revenue, performa cabang, layanan populer, dan karyawan terbaik
- **Role-based Access Control:** Sistem role & permission menggunakan Spatie Permission dan Filament Shield

---

## Tech Stack

| Komponen       | Teknologi                          |
| -------------- | ---------------------------------- |
| Framework      | Laravel 12                         |
| Admin Panel    | Filament v3                        |
| Authorization  | Spatie Permission + Filament Shield|
| Database       | MySQL 8.x                          |
| Frontend Build | Vite + Tailwind CSS v4             |
| Charting       | Flowframe Laravel Trend            |
| Cache/Session  | Redis via Predis                   |
| Testing        | PestPHP v3                         |
| PHP            | >= 8.2                             |

---

## Persyaratan Sistem

- **PHP** >= 8.2 dengan ekstensi berikut:
  - `pdo_mysql`
  - `mbstring`
  - `openssl`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`
  - `bcmath`
  - `redis`
- **Composer** >= 2.x
- **Node.js** >= 18.x dan **npm** >= 9.x
- **MySQL** >= 8.0
- **Redis** >= 6.x
- **Git**

---

## Instalasi

### Quick Setup

Jalankan satu perintah untuk menginstal semua dependensi, membuat file `.env`, generate key, migrasi database, dan build asset:

```bash
git clone <repository-url> nusantara_pangkas_rambut
cd nusantara_pangkas_rambut
composer setup
```

Lalu jalankan seeder untuk data awal:

```bash
php artisan db:seed
```

### Manual Setup

#### 1. Clone Repository

```bash
git clone <repository-url> nusantara_pangkas_rambut
cd nusantara_pangkas_rambut
```

#### 2. Install Dependensi PHP

```bash
composer install
```

#### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Konfigurasi Database & Redis

Buat database MySQL baru:

```sql
CREATE DATABASE db_nusantara CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sesuaikan konfigurasi di `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_nusantara
DB_USERNAME=root
DB_PASSWORD=your_password

SESSION_DRIVER=redis
CACHE_STORE=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### 5. Jalankan Migrasi

```bash
php artisan migrate
```

#### 6. Jalankan Seeder

Seeder akan membuat data awal termasuk roles, users, branches, services, employees, customers, dan transaksi:

```bash
php artisan db:seed
```

Urutan seeder yang dijalankan:
1. `RolePermissionSeeder` — Membuat role dan permission
2. `BranchSeeder` — Data cabang
3. `UserSeeder` — Membuat user default (super admin, admin, kasir)
4. `ServiceSeeder` — Data layanan
5. `EmployeeSeeder` — Data karyawan
6. `CustomerSeeder` — Data pelanggan

#### 7. Generate Permissions Filament Shield

```bash
php artisan shield:generate --all
```

#### 8. Install Dependensi Frontend & Build Asset

```bash
npm install
npm run build
```

#### 9. Buat Symlink Storage

```bash
php artisan storage:link
```

---

## Menjalankan Aplikasi

### Mode Development (Recommended)

Jalankan semua service sekaligus (web server, queue worker, log viewer, dan Vite dev server):

```bash
composer dev
```

Perintah ini akan menjalankan secara bersamaan:
- `php artisan serve` — Web server di `http://localhost:8000`
- `php artisan queue:listen` — Queue worker
- `php artisan pail` — Log viewer (real-time)
- `npm run dev` — Vite dev server (hot reload)

### Mode Manual

Jalankan masing-masing di terminal terpisah:

```bash
# Terminal 1 - Web Server
php artisan serve

# Terminal 2 - Vite Dev Server
npm run dev

# Terminal 3 - Queue Worker (opsional)
php artisan queue:listen
```

### Akses Aplikasi

| URL                           | Keterangan           |
| ----------------------------- | -------------------- |
| `http://localhost:8000`       | Halaman utama        |
| `http://localhost:8000/admin` | Panel admin Filament |

---

## Akun Default

Setelah menjalankan seeder, tersedia akun default berikut. Semua login melalui panel admin di `/admin`.

| Role        | Email                          | Password   | Cabang     |
| ----------- | ------------------------------ | ---------- | ---------- |
| super_admin | `superadmin@example.com`       | `password` | Semua      |
| admin       | `admin.ngaliyan@example.com`   | `password` | Ngaliyan   |
| admin       | `admin.fatmawati@example.com`  | `password` | Fatmawati  |
| cashier     | `kasir.ngaliyan@example.com`   | `password` | Ngaliyan   |
| cashier     | `kasir.fatmawati@example.com`  | `password` | Fatmawati  |

> ⚠️ **Penting:** Segera ubah password default setelah deploy ke production!

---

## Struktur Database

### Entity Relationship

```
User ←→ Branch               (many-to-many via branch_user)
User ← Employee              (one-to-one)
Branch ← Employee            (one-to-many)
Branch ← Transaction         (one-to-many)
Branch ← Reservation         (one-to-many)
Customer ← Transaction       (one-to-many)
Customer ← Reservation       (one-to-many)
Employee ← Transaction       (one-to-many)
Transaction ← TransactionItem      (one-to-many)
TransactionItem → Service/Product  (belongs-to)
Reservation ↔ Service        (many-to-many via reservation_services)
```

### Tabel Utama

| Tabel                  | Deskripsi                                                      |
| ---------------------- | -------------------------------------------------------------- |
| `users`                | Akun staff (guard: web) untuk login ke panel admin             |
| `customers`            | Akun pelanggan (guard: customer) untuk portal pelanggan        |
| `branches`             | Data cabang barbershop (`name`, `slug`, `address`)             |
| `branch_user`          | Pivot table relasi user-branch                                 |
| `employees`            | Data karyawan (`name`, `phone`, `position`, `is_active`)       |
| `services`             | Daftar layanan (`name`, `price`, `duration`, `is_active`)      |
| `products`             | Daftar produk (`name`, `price`, `is_active`)                   |
| `transactions`         | Transaksi (`invoice_number`, `total_amount`, `status`)         |
| `transaction_items`    | Item transaksi (`quantity`, `price`, `subtotal`)               |
| `reservations`         | Booking appointment pelanggan                                  |
| `reservation_services` | Pivot table relasi reservasi-layanan                           |

---

## Struktur Project

```
app/
├── Filament/
│   ├── Pages/              # Halaman custom Filament (Dashboard)
│   ├── Resources/          # CRUD Resources
│   │   ├── BranchResource.php
│   │   ├── CustomerResource.php
│   │   ├── EmployeeResource.php
│   │   ├── ProductResource.php
│   │   ├── ServiceResource.php
│   │   └── TransactionResource.php
│   └── Widgets/            # Widget dashboard analitik (6 widget)
├── Http/
│   ├── Controllers/        # Auth & Customer controllers
│   └── Requests/           # Form request validation
├── Livewire/
│   └── PosKasir.php        # Komponen POS kasir
├── Models/                 # Eloquent Models (11 model)
│   ├── Branch.php
│   ├── Customer.php
│   ├── Employee.php
│   ├── Product.php
│   ├── Reservation.php
│   ├── Service.php
│   ├── Transaction.php
│   ├── TransactionItem.php
│   └── User.php
├── Policies/               # Authorization Policies (7 file)
└── Providers/

database/
├── factories/              # Model Factories untuk testing (8 file)
├── migrations/             # Database Migrations (17 file)
└── seeders/                # Database Seeders (6 file)

routes/
├── web.php                 # Route utama + customer portal + POS
└── auth.php                # Authentication routes

resources/
├── css/                    # Stylesheet
├── js/                     # JavaScript
└── views/                  # Blade Templates
```

---

## Manajemen Role & Permission

Project ini menggunakan **Spatie Laravel Permission** dengan **Filament Shield** untuk mengelola role dan permission secara otomatis.

### Role Default

| Role          | Deskripsi                                                            |
| ------------- | -------------------------------------------------------------------- |
| `super_admin` | Akses penuh ke semua fitur dan semua cabang (bypass Gate)            |
| `admin`       | Manajemen karyawan, transaksi, layanan, produk, pelanggan di cabangnya |
| `cashier`     | Hanya POS kasir, lihat transaksi, layanan, produk, dan pelanggan     |

### Generate Permission

Untuk men-generate ulang permission berdasarkan resource Filament:

```bash
php artisan shield:generate --all
```

### Mengelola Role

Role dan permission bisa dikelola langsung dari panel admin di menu **Shield > Roles** (`/admin/shield/roles`).

---

## Testing

Jalankan test suite:

```bash
composer test
```

Atau langsung dengan artisan:

```bash
php artisan test
```

Untuk menjalankan test spesifik:

```bash
php artisan test --filter=NamaTest
```

---

## Perintah Artisan Berguna

```bash
# Reset database dan jalankan ulang semua seeder
php artisan migrate:fresh --seed

# Clear semua cache
php artisan optimize:clear

# Generate ulang permission Shield
php artisan shield:generate --all

# Menjalankan code formatting dengan Pint
./vendor/bin/pint
```

---

## Deployment ke Production

1. Set environment variables di `.env`:
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   ```

2. Optimalkan aplikasi:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan icons:cache
   php artisan filament:cache-components
   npm run build
   ```

3. Pastikan folder `storage/` dan `bootstrap/cache/` writable oleh web server.

4. Setup queue worker (supervisor recommended) dan cron job untuk scheduler.

---

## Lisensi

Project ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT).
