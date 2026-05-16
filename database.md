# 🗄️ CAMPUS-E — Rancangan Database
**Sistem Prediksi Proaktif Risiko Depresi Mahasiswa**

---

## 📊 Daftar Tabel

| No | Nama Tabel | Fungsi |
|---|---|---|
| 1 | `users` | Data mahasiswa & admin |
| 2 | `self_checks` | Hasil self-check harian |
| 3 | `buku_harian` | Entri curhatan + AI reply |
| 4 | `forum_posts` | Postingan tukar pikiran |
| 5 | `forum_replies` | Balasan postingan forum |
| 6 | `forum_likes` | Like per postingan |
| 7 | `alerts` | Alert krisis terdeteksi |
| 8 | `activity_logs` | Log semua aksi admin |

---

## 1. Tabel `users`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint, PK, AI | |
| `nama` | varchar(100) | Nama lengkap |
| `nim` | varchar(20), unique | Nomor Induk Mahasiswa |
| `email` | varchar(100), unique | Email kampus |
| `no_telepon` | varchar(20) | Nomor telepon mahasiswa |
| `jurusan` | varchar(100) | Contoh: Teknologi Informasi |
| `program_studi` | varchar(100) | Contoh: D4 Teknik Informatika |
| `kontak_darurat` | varchar(20) | Nomor orang tua/wali |
| `password` | varchar(255) | Bcrypt hash |
| `username_anonim` | varchar(50) | Contoh: Langit #089 |
| `is_admin` | tinyint(1), default 0 | 0 = mahasiswa, 1 = admin |
| `remember_token` | varchar(100), null | Breeze bawaan |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## 2. Tabel `self_checks`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint, PK, AI | |
| `user_id` | bigint, FK → users.id | |
| `jawaban_1` | tinyint | Kualitas tidur (1–5) |
| `jawaban_2` | tinyint | Mood hari ini (1–5) |
| `jawaban_3` | tinyint | Tingkat kecemasan (1–5) |
| `jawaban_4` | tinyint | Tingkat motivasi (1–5) |
| `jawaban_5` | tinyint | Tingkat isolasi (1–5) |
| `skor_total` | tinyint | Jumlah semua jawaban (5–25) |
| `teks_gabung` | text | Gabungan jawaban → dikirim ke IndoBERT |
| `label` | tinyint | 0–4 hasil prediksi IndoBERT |
| `risk_level` | varchar(20) | LOW / MEDIUM / HIGH / CRITICAL |
| `confidence` | float | Skor kepercayaan prediksi (%) |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

> **Catatan:** Satu mahasiswa hanya boleh satu record per hari.
> Dicek via unique index `user_id + DATE(created_at)`.

---

## 3. Tabel `buku_harian`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint, PK, AI | |
| `user_id` | bigint, FK → users.id | |
| `isi` | text | Isi curhatan mahasiswa |
| `ai_reply` | text, null | Balasan empatik dari IndoBERT |
| `ai_saran` | json, null | Array saran kegiatan dari AI |
| `label` | tinyint, null | 0–4 hasil prediksi |
| `risk_level` | varchar(20), null | LOW / MEDIUM / HIGH / CRITICAL |
| `confidence` | float, null | Skor kepercayaan (%) |
| `is_analyzed` | tinyint(1), default 0 | 0 = belum, 1 = sudah dianalisis AI |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## 4. Tabel `forum_posts`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint, PK, AI | |
| `user_id` | bigint, FK → users.id | |
| `konten` | text | Isi postingan |
| `label` | tinyint, null | 0–4 hasil prediksi |
| `risk_level` | varchar(20), null | LOW / MEDIUM / HIGH / CRITICAL |
| `is_hidden` | tinyint(1), default 0 | 1 = disembunyikan karena label ≥ 3 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## 5. Tabel `forum_replies`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint, PK, AI | |
| `post_id` | bigint, FK → forum_posts.id | |
| `user_id` | bigint, FK → users.id | |
| `konten` | text | Isi balasan |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## 6. Tabel `forum_likes`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint, PK, AI | |
| `post_id` | bigint, FK → forum_posts.id | |
| `user_id` | bigint, FK → users.id | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

> **Catatan:** Kombinasi `post_id + user_id` harus unique —
> satu mahasiswa hanya bisa like sekali per post.

---

## 7. Tabel `alerts`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint, PK, AI | |
| `user_id` | bigint, FK → users.id | Mahasiswa yang terdeteksi |
| `sumber` | enum | `buku_harian` / `self_check` / `forum_post` |
| `sumber_id` | bigint | ID record dari tabel sumber |
| `label` | tinyint | 3 = DEPRESSION_RISK, 4 = SUICIDAL_IDEATION |
| `risk_level` | varchar(20) | HIGH / CRITICAL |
| `confidence` | float | Skor kepercayaan (%) |
| `kata_kunci` | json, null | Array kata kunci berbahaya yang terdeteksi |
| `cuplikan_teks` | varchar(255) | Potongan teks yang memicu alert |
| `is_handled` | tinyint(1), default 0 | 0 = belum, 1 = sudah ditindaklanjuti |
| `handled_by` | bigint, null, FK → users.id | Admin yang menindaklanjuti |
| `handled_at` | timestamp, null | Waktu ditindaklanjuti |
| `identity_opened` | tinyint(1), default 0 | 0 = belum, 1 = identitas pernah dibuka |
| `opened_by` | bigint, null, FK → users.id | Admin yang membuka identitas |
| `opened_at` | timestamp, null | Waktu identitas dibuka |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## 8. Tabel `activity_logs`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint, PK, AI | |
| `aksi` | enum | `alert_dibuat` / `identitas_dibuka` / `alert_ditindaklanjuti` |
| `severity` | enum | `waspada` / `kritis` |
| `alert_id` | bigint, FK → alerts.id | Alert yang terkait |
| `target_user_id` | bigint, FK → users.id | Mahasiswa yang terkait |
| `actor_id` | bigint, null, FK → users.id | Admin yang melakukan aksi (null = sistem) |
| `actor_label` | varchar(100) | "Dr. Sarah Wijaya" atau "Sistem" |
| `detail` | text | Deskripsi lengkap aksi |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

> **Catatan:** Tabel ini bersifat append-only —
> tidak ada operasi `UPDATE` atau `DELETE` setelah data masuk.

---

## 🔗 Relasi Antar Tabel

```
users ──────────────────────────────────────────────────────────┐
  │                                                              │
  ├── self_checks       (user_id → users.id)                    │
  ├── buku_harian       (user_id → users.id)                    │
  ├── forum_posts       (user_id → users.id)                    │
  │     └── forum_replies  (post_id  → forum_posts.id)          │
  │     └── forum_likes    (post_id  → forum_posts.id)          │
  │                                                              │
  ├── alerts            (user_id   → users.id)                  │
  │                     (handled_by→ users.id)                  │
  │                     (opened_by → users.id)                  │
  │                     sumber_id bisa dari:                     │
  │                       · self_checks.id                       │
  │                       · buku_harian.id                       │
  │                       · forum_posts.id                       │
  │                                                              │
  └── activity_logs     (target_user_id → users.id)             │
                        (actor_id       → users.id)             │
                        (alert_id       → alerts.id) ───────────┘
```

---

## 📐 Aturan Bisnis Database

| Aturan | Implementasi |
|---|---|
| Satu self-check per hari per mahasiswa | Unique index `user_id + DATE(created_at)` di `self_checks` |
| Satu like per post per mahasiswa | Unique index `post_id + user_id` di `forum_likes` |
| Post otomatis disembunyikan jika label ≥ 3 | `is_hidden = 1` saat insert `forum_posts` |
| Alert hanya dibuat jika label ≥ 3 | Dicek di `AIService` sebelum insert `alerts` |
| Setiap pembukaan identitas wajib dicatat | Insert `activity_logs` bersamaan update `alerts.opened_at` |
| `activity_logs` tidak bisa diedit/dihapus | Tidak ada `UPDATE` / `DELETE` di `ActivityLog` model |

---

## 🏷️ Mapping Label IndoBERT

| Label | Mental State | Risk Level | Warna |
|---|---|---|---|
| 0 | NORMAL | LOW | 🟢 Hijau |
| 1 | MENTAL_FATIGUE | LOW | 🟡 Kuning |
| 2 | EMOTIONAL_STRESS | MEDIUM | 🟠 Oranye |
| 3 | DEPRESSION_RISK | HIGH | 🔴 Merah |
| 4 | SUICIDAL_IDEATION | CRITICAL | 🟣 Ungu |

> Label ≥ 3 → trigger insert ke tabel `alerts` secara otomatis.

---

## 📅 Mapping Skor Self-Check → Mood Tracker

| Skor Total | Kategori | Warna Kalender |
|---|---|---|
| 20 – 25 | Baik | 🟢 Hijau |
| 13 – 19 | Waspada | 🟡 Kuning |
| 5 – 12 | Kritis | 🔴 Merah |
| Tidak ada data | — | ⬜ Abu-abu |
