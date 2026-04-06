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

- **Multi-cabang (Branch):** Kelola beberapa cabang barbershop dalam satu sistem
- **Manajemen Karyawan:** Data karyawan terhubung dengan cabang dan akun user
- **Manajemen Pelanggan:** Pencatatan data pelanggan lengkap
- **Manajemen Layanan:** Daftar layanan beserta harga
- **Transaksi:** Pencatatan transaksi dengan nomor invoice otomatis (format `INV-YYYYMMDD-NNNN`), multi-item, dan kalkulasi subtotal otomatis
- **Role-based Access Control:** Sistem role & permission menggunakan Spatie Permission dan Filament Shield
- **Dashboard Admin:** Panel admin lengkap menggunakan Filament v3

---

## Tech Stack

| Komponen       | Teknologi                          |
| -------------- | ---------------------------------- |
| Framework      | Laravel 12                         |
| Admin Panel    | Filament v3                        |
| Authorization  | Spatie Permission + Filament Shield|
| Database       | SQLite (default) / MySQL / MariaDB |
| Frontend Build | Vite + Tailwind CSS v4             |
| Charting       | Flowframe Laravel Trend            |
| Cache/Session  | Redis (opsional) via Predis        |
| PHP            | >= 8.2                             |

---

## Persyaratan Sistem

- **PHP** >= 8.2 dengan ekstensi berikut:
  - `pdo_sqlite` (untuk SQLite) atau `pdo_mysql` (untuk MySQL/MariaDB)
  - `mbstring`
  - `openssl`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`
  - `bcmath`
- **Composer** >= 2.x
- **Node.js** >= 18.x dan **npm** >= 9.x
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
php artisan webpush:vapid
```

#### 4. Konfigurasi Database

Secara default, project ini menggunakan **SQLite**. File `.env.example` sudah dikonfigurasi untuk SQLite.

**Opsi A — SQLite (Default):**

Buat file database SQLite:

```bash
touch database/database.sqlite
```

Pastikan di `.env`:

```dotenv
DB_CONNECTION=sqlite
```

**Opsi B — MySQL/MariaDB:**

Buat database baru di MySQL/MariaDB, lalu ubah `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nusantara_pangkas_rambut
DB_USERNAME=root
DB_PASSWORD=your_password
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
2. `UserSeeder` — Membuat user default (super admin, admin, cashier)
3. `BranchSeeder` — Data cabang
4. `ServiceSeeder` — Data layanan
5. `EmployeeSeeder` — Data karyawan
6. `CustomerSeeder` — Data pelanggan
7. `TransactionSeeder` — Data transaksi contoh

#### 7. Install Dependensi Frontend & Build Asset

```bash
npm install
npm run build
```

#### 8. Buat Symlink Storage (opsional)

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
- `php artisan schedule:work` — Background scheduler untuk mengeksekusi Pembatalan Reservasi Otomatis & Notifikasi

### Mode Manual

Jalankan masing-masing di terminal terpisah:

```bash
# Terminal 1 - Web Server
php artisan serve

# Terminal 2 - Vite Dev Server (Agar perubahan layout dan animasi CSS bisa muncul)
npm run dev

# Terminal 3 - Scheduler (Wajib untuk menjalankan robot Auto-Cancel Reservasi & Notifikasi)
php artisan schedule:work

# Terminal 4 - Queue Worker (opsional)
php artisan queue:listen
```

### Akses Aplikasi

| URL                           | Keterangan           |
| ----------------------------- | -------------------- |
| `http://localhost:8000`       | Halaman utama        |
| `http://localhost:8000/admin` | Panel admin Filament |

---

## Akun Default

Setelah menjalankan seeder, tersedia 3 akun default:

| Role        | Email                    | Password   |
| ----------- | ------------------------ | ---------- |
| Super Admin | `superadmin@example.com` | `password` |
| Admin       | `admin@example.com`      | `password` |
| Cashier     | `cashier@example.com`    | `password` |

> ⚠️ **Penting:** Segera ubah password default setelah deploy ke production!

---

## Struktur Database

### Entity Relationship

```
User ←→ Branch          (many-to-many via branch_user)
User ← Employee          (one-to-one)
Branch ← Employee        (one-to-many)
Branch ← Transaction     (one-to-many)
Customer ← Transaction   (one-to-many)
Employee ← Transaction   (one-to-many)
Transaction ← TransactionItem  (one-to-many)
Service ← TransactionItem      (one-to-many)
```

### Tabel Utama

| Tabel              | Deskripsi                                                |
| ------------------ | -------------------------------------------------------- |
| `users`            | Data user yang bisa login ke sistem                      |
| `branches`         | Data cabang barbershop (`name`, `slug`, `address`)       |
| `branch_user`      | Pivot table relasi user-branch                           |
| `employees`        | Data karyawan (`name`, `phone`, `position`, `is_active`) |
| `customers`        | Data pelanggan (`name`, `phone`, `email`, `gender`)      |
| `services`         | Daftar layanan (`name`, `price`, `is_active`)            |
| `transactions`     | Transaksi (`invoice_number`, `total_amount`, `status`)   |
| `transaction_items`| Item transaksi (`quantity`, `price`, `subtotal`)         |

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
│   │   ├── ServiceResource.php
│   │   └── TransactionResource.php
│   └── Widgets/            # Widget dashboard
├── Http/Controllers/
├── Models/                 # Eloquent Models
│   ├── Branch.php
│   ├── Customer.php
│   ├── Employee.php
│   ├── Service.php
│   ├── Transaction.php
│   ├── TransactionItem.php
│   └── User.php
├── Policies/               # Authorization Policies
└── Providers/

database/
├── factories/              # Model Factories untuk testing
├── migrations/             # Database Migrations
└── seeders/                # Database Seeders

resources/
├── css/                    # Stylesheet
├── js/                     # JavaScript
└── views/                  # Blade Templates

config/
├── filament-shield.php     # Konfigurasi Filament Shield
└── ...
```

---

## Manajemen Role & Permission

Project ini menggunakan **Spatie Laravel Permission** dengan **Filament Shield** untuk mengelola role dan permission secara otomatis.

### Role Default

| Role          | Deskripsi                                   |
| ------------- | ------------------------------------------- |
| `super-admin` | Akses penuh ke semua fitur (bypass Gate)    |
| `admin`       | Akses administrasi                          |
| `cashier`     | Akses kasir (terbatas)                      |

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
