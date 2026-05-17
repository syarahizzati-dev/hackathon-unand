<p align="center">
  <img src="public/favicon.ico" alt="CAMPUS-E Logo" width="100">
  <h1 align="center">CAMPUS-E</h1>
  <p align="center"><strong>Sistem Prediksi Risiko Depresi & Monitoring Kesehatan Mental Mahasiswa</strong></p>
</p>

## 📌 Tentang Project

**CAMPUS-E** adalah sebuah platform aplikasi berbasis web inovatif yang bertujuan untuk membantu memantau dan memprediksi risiko depresi pada mahasiswa. Dengan mengutamakan privasi dan anonimitas, sistem ini menjadi ruang aman bagi mahasiswa untuk melakukan *self-check*, melacak mood harian, menulis buku harian (curhat), serta berinteraksi dalam forum tukar pikiran. 

Sistem ini didukung oleh **Artificial Intelligence (AI)** menggunakan model bahasa **IndoBERT** melalui servis FastAPI, yang secara otomatis menganalisis teks curhatan mahasiswa untuk mendeteksi risiko depresi atau kecenderungan *suicide/self-harm* secara real-time. Jika terdeteksi risiko krisis, sistem akan memberikan **Alert Darurat** kepada Admin/Konselor kampus.

---

## 🚀 Fitur Utama

### 🧑‍🎓 Portal Mahasiswa (Anonim & Aman)
- **Self-Check Harian:** Kuesioner cepat 30 detik untuk memantau kondisi mental.
- **Mood Tracker:** Kalender pelacak mood harian dengan indikator warna berbasis tingkat risiko.
- **Buku Harian AI:** Ruang curhat privat. AI akan memberikan respons empatik dan saran kegiatan berdasarkan analisis sentimen teks (IndoBERT).
- **Forum Tukar Pikiran:** Ruang diskusi aman dan anonim untuk saling mendukung sesama mahasiswa.

### 👨‍⚕️ Portal Admin & Konselor
- **Dashboard Statistik:** Visualisasi tren kondisi mental mahasiswa (Zona Aman, Waspada, Kritis).
- **Alert Krisis Real-time:** Notifikasi seketika jika sistem mendeteksi kata kunci berbahaya/kritis dari curhatan mahasiswa.
- **Sistem Buka Identitas Darurat:** Fitur *break-glass* untuk membuka identitas mahasiswa berisiko tinggi guna penanganan darurat.
- **Log Aktivitas (Audit Trail):** Rekam jejak *append-only* dari semua tindakan admin (menjamin akuntabilitas dan privasi mahasiswa).

---

## 🛠️ Teknologi yang Digunakan

- **Frontend:** Laravel Blade, Livewire Volt, Tailwind CSS, Vite.
- **Backend (Web App):** Laravel 12, PHP 8.2+.
- **Backend (AI Service):** FastAPI, Python 3.10+, PyTorch, Transformers (Hugging Face IndoBERT).
- **Database:** MySQL.

---

## ⚙️ Panduan Instalasi & Setup

Sistem ini terdiri dari dua bagian utama yang harus dijalankan secara bersamaan: **Web App (Laravel)** dan **AI Service (FastAPI)**.

### 1. Persiapan Web App (Laravel)

1. **Clone Repository**
   ```bash
   git clone https://github.com/syarahizzati-dev/hackathon-unand.git
   cd hackathon-unand
   ```

2. **Install Dependensi PHP & Node.js**
   ```bash
   composer install
   npm install
   ```

3. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database MySQL**
   Buat database baru di MySQL (misal: `campus_e`), kemudian sesuaikan file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=campus_e
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Migrasi & Seeding Database**
   Perintah ini akan membuat struktur tabel sekaligus membuat akun dummy Mahasiswa dan Admin.
   ```bash
   php artisan migrate:fresh --seed
   ```

### 2. Persiapan AI Service (FastAPI)

1. Masuk ke direktori `fastapi`:
   ```bash
   cd fastapi
   ```

2. **Buat & Aktifkan Virtual Environment (Direkomendasikan)**
   ```bash
   python -m venv venv
   # Di Windows:
   venv\Scripts\activate
   # Di Mac/Linux:
   source venv/bin/activate
   ```

3. **Install Dependensi Python**
   ```bash
   pip install -r requirements.txt
   ```
   *(Sistem membutuhkan `numpy < 2.0.0` untuk kompatibilitas IndoBERT).*

---

## 🏃‍♂️ Cara Menjalankan Aplikasi

Anda membutuhkan **3 terminal terpisah** untuk menjalankan keseluruhan sistem CAMPUS-E.

**Terminal 1: AI Service (FastAPI)**
```bash
cd fastapi
# Pastikan virtual environment sudah aktif
python run.py
# FastAPI akan berjalan di http://127.0.0.1:8000
```

**Terminal 2: Web App (Laravel)**
```bash
# Di direktori utama project (hackathon-unand)
php artisan serve --port=8080
# Laravel akan berjalan di http://127.0.0.1:8080
```

**Terminal 3: Asset Bundler (Vite)**
```bash
# Di direktori utama project (hackathon-unand)
npm run dev
```

---

## 🔑 Kredensial Akses Default

Setelah melakukan `migrate:fresh --seed`, Anda dapat menggunakan akun berikut untuk testing:

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@kampus.ac.id` | `admin123` |
| **Konselor** | `konselor@kampus.ac.id` | `admin123` |
| **Mahasiswa 1** | `mahasiswa1@test.com` | `password123` |
| **Mahasiswa 2** | `mahasiswa2@test.com` | `password123` |

Akses Portal Admin di: **`http://127.0.0.1:8080/admin`**  
Akses Portal Mahasiswa di: **`http://127.0.0.1:8080/`**

---

## 🔒 Keamanan & Privasi

CAMPUS-E didesain dengan memprioritaskan privasi penggunanya:
- Identitas mahasiswa (Nama, NIM) **disembunyikan** (anonim) dari sesama pengguna maupun Admin pada kondisi normal.
- Tombol **"Buka Identitas"** hanya tersedia untuk Admin jika AI mendeteksi *Alert Krisis* tingkat tinggi.
- Setiap aktivitas pembukaan identitas darurat akan dicatat secara permanen di dalam **Log Aktivitas** dan tidak dapat dihapus (*append-only audit trail*).

---
*Dibuat untuk Hackathon Universitas Andalas.*
