# Sistem Informasi Administrasi FEB UNJ

Sistem Informasi Administrasi Surat untuk Fakultas Ekonomi Universitas Negeri Jakarta (FEB UNJ). Aplikasi ini membantu dalam pengelolaan surat menyurat secara digital dengan fitur penomoran otomatis, tracking surat, dan manajemen data master.

---

## 🛠️ Tech Stack

- **Laravel 10.x** - PHP Framework
- **PHP 8.1+** - Programming Language
- **MySQL 8.0** - Relational Database
- **Tailwind CSS 3.x** - Utility-first CSS Framework
- **Alpine.js** - Lightweight JavaScript Framework

---

## 💻 Requirements

- PHP >= 8.1
- Composer >= 2.5
- Node.js >= 18.x
- NPM >= 9.x
- MySQL >= 8.0 atau MariaDB >= 10.3

---

## 📦 Instalasi & Running Project

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/sistem-informasi-administrasi-FEB-UNJ.git
cd sistem-informasi-administrasi-FEB-UNJ
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

```bash
# Windows
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

```bash
php artisan key:generate
```

### 4. Konfigurasi Database

Buat database baru di MySQL:

```sql
CREATE DATABASE sia_feb_unj CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sia_feb_unj
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### 5. Migrasi Database & Seeder

```bash
php artisan migrate --seed
```

### 6. Build Assets

```bash
npm run dev
```

### 7. Storage Link

```bash
php artisan storage:link
```

### 8. Running Project

Buka **2 terminal** secara bersamaan:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

Buka browser: **http://localhost:8000**

---

## 🔑 Default Login

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@feb.unj.ac.id | password123 |
| Operator | operator@feb.unj.ac.id | password123 |
| Viewer | viewer@feb.unj.ac.id | password123 |

⚠️ **PENTING**: Ubah password setelah login pertama kali!

---

## 🚀 Production Build

```bash
# Build assets untuk production
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

---

## 📝 Lisensi

MIT License - © 2024 Fakultas Ekonomi Universitas Negeri Jakarta
