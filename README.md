# CAMPUS-E

## Instalasi Project

### 1. Clone Repository

```bash
git clone https://github.com/syarahizzati-dev/hackathon-unand.git
```

```bash
cd campus-e
```

---

# 2. Install Dependency Laravel

```bash
composer install
```

```bash
npm install
```

---

# 3. Setup Environment

Copy file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

---

# 4. Setup Database MySQL

Buat database MySQL:

```sql
CREATE DATABASE campus_e;
```

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campus_e
DB_USERNAME=root
DB_PASSWORD=
```

---

# 5. Migrasi Database

```bash
php artisan migrate
```

---

# 6. Jalankan Laravel

```bash
php artisan serve
```

---

# 7. Jalankan Vite

```bash
npm run dev
```

---
