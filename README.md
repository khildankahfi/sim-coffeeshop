# ☕ CAFFEINE

Sistem Informasi Manajemen Coffeeshop berbasis web — dibangun dengan Laravel 11, Vite, dan vanilla CSS tanpa framework UI eksternal.

---

## Mata Kuliah

Sistem Informasi Manajemen

---

## Anggota Kelompok

1. Berliana Nidia Meiningrum — 24091397142
2. Arif Feredais Prakoso — 24091397146
3. Anindya Calista Raniah — 24091397157
4. Khildan Ash Kahfi — 24091397159
5. M Kaysa Handoko Putra — 24091397170

---

## Fitur

### 👑 Admin
| Fitur | Deskripsi |
|---|---|
| **Dashboard** | Statistik harian, grafik pendapatan 7 hari, transaksi terbaru, stok menipis |
| **Kasir / POS** | Point of Sale dengan keranjang interaktif, quick amount, dan kalkulasi kembalian otomatis |
| **Riwayat Transaksi** | Lihat semua transaksi dengan filter tanggal dan detail nota |
| **Produk / Menu** | CRUD produk lengkap dengan upload foto, stok, status aktif/nonaktif |
| **Kategori** | Kelola kategori menu |
| **Laporan** | Laporan pendapatan dengan filter periode (hari ini, minggu, bulan, custom), produk terlaris, tren grafik |
| **Manajemen Karyawan** | CRUD akun admin dan kasir |
| **Profil** | Update nama, email, password |

### 💳 Kasir
| Fitur | Deskripsi |
|---|---|
| **Kasir / POS** | Akses penuh ke fitur transaksi |
| **Riwayat** | Hanya melihat transaksi milik sendiri |
| **Profil** | Update data pribadi |

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11 |
| Frontend | Blade Templates + Vite |
| Styling | Custom CSS (tanpa Tailwind/Bootstrap) |
| Database | MySQL |
| Auth | Laravel Breeze (dikustomisasi) |
| Chart | Chart.js 4.4 |
| JS | Alpine.js + Axios |

---

## Struktur Database

```
users           — akun admin & kasir (role: admin/kasir)
categories      — kategori menu (kopi, non-kopi, makanan, dll.)
products        — produk/menu dengan stok, harga, foto
orders          — header transaksi (invoice, total, kembalian, status)
order_items     — detail item per transaksi (snapshot nama & harga)
```

### Relasi
```
User       ──< Order         (satu kasir bisa buat banyak order)
Order      ──< OrderItem     (satu order punya banyak item)
OrderItem  >── Product       (setiap item merujuk ke satu produk)
Product    >── Category      (setiap produk masuk satu kategori)
```

---

## Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL

### Langkah Setup

**1. Clone & install dependency**
```bash
git clone <repo-url>
cd sim-coffeeshop

composer install
npm install
```

**2. Konfigurasi environment**
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME="SIM Coffeeshop"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sim_coffeeshop
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=false
```

> ⚠️ Pastikan `APP_URL` sama persis dengan URL yang diakses di browser. Kalau buka di `127.0.0.1:8000` jangan isi `localhost`, begitu sebaliknya.

**3. Buat database & jalankan migrasi**
```bash
php artisan migrate
php artisan db:seed        # (opsional) isi data dummy
php artisan storage:link   # untuk foto produk
```

**4. Jalankan server**
```bash
# Terminal 1 — Laravel
php artisan serve

# Terminal 2 — Vite (asset bundler)
npm run dev
```

Buka browser: `http://127.0.0.1:8000`

---

## Akun Default

Setelah seeder dijalankan:

| Role | Email | Password |
|---|---|---|
| Admin | admin@coffeeshop.com | password |
| Kasir | kasir@coffeeshop.com | password |

> Ganti password setelah login pertama melalui halaman **Profil**.

---

## Struktur Direktori Utama

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── OrderController.php       # POS & riwayat transaksi
│   │   ├── ProductController.php     # CRUD produk + upload foto
│   │   ├── CategoryController.php
│   │   ├── ReportController.php      # Laporan dengan filter periode
│   │   ├── UserController.php        # Manajemen karyawan
│   │   └── ProfileController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php        # Proteksi route by role
│   └── Requests/                     # Form Request Validation
├── Models/
│   ├── Order.php                     # Auto-generate invoice number
│   ├── OrderItem.php                 # Snapshot harga saat transaksi
│   ├── Product.php                   # Accessor: formatted_price, image_url
│   ├── Category.php
│   └── User.php                      # isAdmin() / isKasir() helper

resources/
├── css/app.css                       # Design system lengkap (1300+ baris)
├── js/app.js                         # Alpine.js + Axios
└── views/
    ├── layouts/
    │   ├── app.blade.php             # Layout utama (sidebar + topbar)
    │   └── guest.blade.php           # Layout halaman auth (split panel)
    ├── dashboard/index.blade.php
    ├── orders/
    │   ├── create.blade.php          # Kasir / POS
    │   ├── index.blade.php           # Riwayat transaksi
    │   └── show.blade.php            # Detail nota
    ├── products/                     # CRUD produk
    ├── categories/                   # CRUD kategori
    ├── reports/index.blade.php       # Laporan penjualan
    ├── users/                        # Manajemen karyawan
    └── profile/                      # Edit profil & password
```

---

## Lisensi

Proyek ini dibuat untuk keperluan final project — D4 Manajemen Informatika.