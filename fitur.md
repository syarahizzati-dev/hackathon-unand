================================================================
CAMPUS-E — Daftar Halaman & Fitur Sesuai Desain Aplikasi
Sistem Prediksi Proaktif Risiko Depresi Mahasiswa
================================================================


================================================================
HALAMAN 1. LOGIN MAHASISWA  (rute: "/")
================================================================

Tampilan:
- Latar gradien biru muda (from-blue-50 via-white to-blue-50).
- Logo kotak biru bertuliskan "C".
- Judul: "CAMPUS-E".
- Sub-judul: "Sistem Prediksi Proaktif Risiko Depresi Mahasiswa".
- Kartu form login (rounded, border, shadow).

Fitur:
- Input Email (ikon Mail) — placeholder "email@example.com".
- Input Password (ikon Lock) — placeholder "Masukkan password".
- Tombol mata (Eye / EyeOff) untuk show/hide password.
- Tombol "Masuk" → navigasi ke /student-dashboard.
- Tautan "Belum punya akun? Daftar sekarang" → /register.
- Tidak ada tautan apapun menuju login admin.


================================================================
HALAMAN 2. REGISTRASI MAHASISWA  (rute: "/register")
================================================================

Tampilan:
- Layout sama (gradien biru, logo "C").
- Judul: "Daftar CAMPUS-E".
- Sub-judul: "Buat akun mahasiswa baru".
- Kartu form (max-w-2xl, grid 2 kolom di desktop).

Fitur (field form) — urutan sesuai desain:
- Nama Lengkap (ikon User) — placeholder "Nama lengkap".
- NIM (ikon GraduationCap) — placeholder "Nomor Induk Mahasiswa".
- Email (ikon Mail) — placeholder "email@example.com".
- No. Telepon (ikon Phone) — placeholder "08123456789".
- Jurusan — placeholder "Teknologi Informasi".
- Program Studi — placeholder "D3 Manajemen Informatika".
- Kontak Darurat (ikon Phone) — placeholder "Nomor orang tua/wali".
- Password (ikon Lock, min. 8 karakter) — toggle show/hide.
- Konfirmasi Password (ikon Lock).
- Validasi: password & konfirmasi harus cocok (alert "Password tidak cocok!").
- Tombol "Daftar" → kembali ke "/".
- Tautan "Sudah punya akun? Masuk di sini" → "/".


================================================================
HALAMAN 3. LOGIN ADMIN  (rute tersembunyi: "/admin")
================================================================

Tampilan:
- Layout & branding sama dengan login mahasiswa (logo "C", judul "CAMPUS-E", sub-judul sama).
- Tidak ada tautan menuju area mahasiswa maupun registrasi.

Fitur:
- Input Email (ikon Mail) — placeholder "admin@kampus.ac.id".
- Input Password (ikon Lock) — placeholder "Masukkan password".
- Toggle show/hide password (Eye / EyeOff).
- Tombol "Masuk" → navigasi ke /admin-dashboard.


================================================================
HALAMAN 4. DASHBOARD MAHASISWA  (rute: "/student-dashboard")
================================================================

Header (sticky):
- Logo "C" + judul "CAMPUS-E" + label "Dashboard Mahasiswa".
- Tombol "Keluar" (ikon LogOut) → kembali ke "/".

Banner Self-check Harian (di bawah header, bisa ditutup):
- Ikon CheckCircle.
- Teks: "Self-check harian tersedia" + "Hanya 30 detik untuk cek kondisi kamu hari ini".
- Tombol "Mulai" → membuka kuesioner.
- Tombol X untuk menutup banner.

Tab Navigasi (sticky):
- Beranda (ikon Bell)
- Mood Tracker (ikon Calendar)
- Buku Harian (ikon BookOpen)
- Tukar Pikiran (ikon Users)

Tab "Beranda":
- Kartu sambutan "Selamat Datang!" + deskripsi:
  "CAMPUS-E adalah sistem yang membantu kamu memantau kesehatan
   mental dengan aman dan anonim."
- Tiga kotak statistik:
    • 30s — "Self-check harian"
    • 100% — "Anonim & aman"
    • 24/7 — "Tempat curhat aman"
- Kartu "Aktivitas Terakhir":
    • titik hijau — "Self-check selesai" — "Hari ini, 08:30"
    • titik biru — "Menulis Buku Harian" — "Kemarin, 20:15"


================================================================
HALAMAN 5. SELF-CHECK HARIAN  (banner dari Dashboard Mahasiswa)
================================================================

Tampilan:
- Judul "Self-check Harian".
- Indikator progres: "Pertanyaan X dari 5".
- Progress bar biru.

Daftar 5 Pertanyaan:
1) "Seberapa baik kamu tidur semalam?"
2) "Bagaimana mood kamu hari ini?"
3) "Apakah kamu merasa cemas atau khawatir?"
4) "Seberapa termotivasi kamu untuk melakukan aktivitas?"
5) "Apakah kamu merasa sendirian atau terisolasi?"

Pilihan Jawaban (5 tombol berwarna):
- Sangat Baik (hijau tua, nilai 5)
- Baik (hijau, nilai 4)
- Biasa (kuning, nilai 3)
- Kurang (oranye, nilai 2)
- Buruk (merah, nilai 1)

Layar Selesai:
- Ikon CheckCircle hijau besar.
- Teks "Selesai!" + "Terima kasih sudah mengisi self-check hari ini".


================================================================
HALAMAN 6. MOOD TRACKER  (tab "Mood Tracker" di Dashboard)
================================================================

Kartu Kalender:
- Judul "Mood Tracker".
- Navigasi bulan: tombol ChevronLeft / ChevronRight + nama bulan
  (format Indonesia, mis. "Mei 2026").
- Header hari: Min, Sen, Sel, Rab, Kam, Jum, Sab.
- Grid tanggal — tiap tanggal diwarnai sesuai mood:
    • Hijau = Baik (skor ≥ 4)
    • Kuning = Waspada (skor ≥ 3)
    • Merah = Kritis (skor < 3)
    • Abu = belum ada data
- Hari ini ditandai ring biru.
- Tooltip per tanggal: "[hari] - [label mood]".

Kartu "Keterangan":
- Hijau — "Baik" — "Kondisi positif".
- Kuning — "Waspada" — "Perlu perhatian".
- Merah — "Kritis" — "Butuh bantuan".

Kartu "Statistik Bulan Ini":
- 8 hari — Kondisi Baik (hijau).
- 4 hari — Waspada (kuning).
- 1 hari — Kritis (merah).


================================================================
HALAMAN 7. BUKU HARIAN  (tab "Buku Harian" di Dashboard)
================================================================

Header Kartu:
- Lingkaran biru ikon BookOpen.
- Judul "Buku Harian".
- Sub-judul: "Tempat Curhat Aman & Anonim".
- Info box biru: "🔒 Tuliskan apa saja yang kamu rasakan hari ini.
  Ini adalah ruang aman kamu. Sistem kami akan menjaga privasi
  tulisanmu sambil memantau kesehatan mentalmu secara diam-diam."

Daftar Entri:
- Bila kosong: ikon BookOpen samar + "Buku harianmu masih kosong."
  + "Mulai tulis apa yang kamu rasakan hari ini."
- Tiap entri ditampilkan sebagai kartu berisi:
    • Avatar bulat ikon User.
    • Nama samaran "Kamu (Langit #089)".
    • Tanggal + jam (format Indonesia, weekday lengkap).
    • Isi tulisan (whitespace-pre-wrap).

Area Input (bawah):
- Textarea — placeholder "Tulis curhatan atau perasaanmu hari ini..."
- Enter (tanpa Shift) untuk menyimpan.
- Tombol "Simpan Curhatan" (ikon Send) — disabled jika kosong.

Kartu Saran AI (muncul di bagian PALING BAWAH halaman setelah
mahasiswa mengirim curhatan):
- Indikator sementara: badge biru "AI sedang mengetik" dengan
  tiga titik animasi bounce (muncul saat sistem menganalisis).
- Setelah analisis selesai, badge berubah menjadi kartu saran:
    • Ikon Sparkles dalam lingkaran biru.
    • Label "CAMPUS-E AI Assistant".
    • Paragraf tanggapan empatik berdasarkan isi curhatan.
    • Sub-kartu putih "Saran Kegiatan Untukmu:" (ikon Activity)
      berisi daftar aktivitas yang disarankan (bullet biru).
- Kartu ini bersifat informatif, tidak menggantikan konseling.
- Sistem tetap menyimpan & menganalisis tulisan untuk deteksi
  risiko depresi di latar belakang.


================================================================
HALAMAN 8. TUKAR PIKIRAN (FORUM ANONIM)  (tab "Tukar Pikiran")
================================================================

Header Kartu:
- Judul "Tukar Pikiran".
- Sub-judul "Berbagi dan saling mendukung secara anonim".
- Tombol "Posting Baru" (ikon Plus).

Form Posting Baru (muncul saat tombol diklik):
- Info nama samaran otomatis, contoh: "Kamu akan muncul sebagai: Langit #042".
- Textarea — placeholder "Bagikan pikiran atau perasaan kamu...".
- Tombol "Batal" & "Posting".

Daftar Postingan (contoh data):
1) Langit #042 — "Kadang saya merasa overwhelmed dengan tugas kuliah
   dan ekspektasi keluarga. Ada yang merasakan hal yang sama?"
   • 12 like, 1 balasan.
   • Balasan Awan #156: "Saya juga merasakan hal yang sama. Yang
     membantu saya adalah membuat to-do list kecil setiap hari."
2) Bintang #089 — "Terima kasih untuk sistem ini. Rasanya lega bisa
   berbagi tanpa khawatir dijudge."
   • 24 like, 0 balasan.

Aksi per Post:
- Tombol like (ikon Heart) — tambah jumlah suka.
- Tombol balasan (ikon MessageCircle) — buka input balasan.
- Input balasan + tombol kirim (ikon Send), Enter untuk kirim.
- Setiap balasan menampilkan avatar, nama samaran, tanggal/jam,
  dan isi pesan.


================================================================
HALAMAN 9. DASHBOARD ADMIN  (rute: "/admin-dashboard")
================================================================

Header (sticky):
- Logo "C" + "CAMPUS-E Admin".
- Sub-judul: "Sistem Monitoring Kesehatan Mental".
- Tombol "Keluar" → kembali ke "/admin".

Tab Navigasi:
- Dashboard (ikon BarChart3)
- Alert Krisis (ikon AlertTriangle)
- Log Aktivitas (ikon FileText)

Tab "Dashboard" — Kartu KPI (4 kolom):
- Total Mahasiswa — 1247 (ikon Users + TrendingUp hijau).
- Zona Aman — 1089 (87.3%) — kartu hijau.
- Waspada — 132 (10.6%) — kartu kuning.
- Kritis — 26 (2.1%) — kartu merah.

Kartu "Distribusi Status Mental":
- Bar progres untuk Zona Aman / Waspada / Kritis dengan jumlah
  mahasiswa di sebelah kanan.

Kartu "Tren Minggu Ini":
- Self-check completion — +12% (hijau).
- AI chat sessions — +8% (biru).
- Forum posts — +15% (ungu).

Kartu "Jurusan Terbanyak":
- Teknologi Informasi — 342.
- Manajemen — 289.
- Psikologi — 256.


================================================================
HALAMAN 10. Peringatan  (tab "Peringatan" di Admin)
================================================================

Banner Atas (merah):
- Ikon AlertTriangle + judul "Peringatan Real-time".
- Deskripsi: "Sistem mendeteksi kata kunci berbahaya dari curhatan mahasiswa di buku harian. Segera tindak lanjuti kasus dengan tingkat keparahan tinggi."

Daftar Alert (kartu, contoh data):

Alert 1 — KRITIS (border merah)
- Waktu deteksi (ikon Clock).
- Kata kunci terdeteksi: "tidak ingin hidup", "menyakiti diri".
- Pesan: "Saya merasa sangat lelah dengan semuanya. Tidak ingin hidup
  seperti ini lagi..."
- Tombol "Buka Identitas Darurat" (ikon Eye).

Alert 2 — KRITIS
- Kata kunci: "bunuh diri", "tidak berguna".
- Pesan: "Aku tidak berguna untuk siapa-siapa. Mungkin lebih baik jika
  aku tidak ada."

Alert 3 — WASPADA (border kuning)
- Kata kunci: "putus asa", "tidak ada harapan".
- Pesan: "Saya sudah tidak tahu harus bagaimana lagi. Rasanya tidak
  ada harapan."

Saat tombol "Buka Identitas Darurat" ditekan:
- Berubah menjadi "Sembunyikan Identitas" (ikon EyeOff).
- Muncul kartu identitas dengan border biru berisi:
    • Notif "Identitas dibuka pada [tanggal-jam]".
    • Nama Lengkap (mis. Ahmad Rizki Pratama).
    • NIM (mis. 2024010001).
    • Jurusan / Program Studi (mis. Teknologi Informasi -
      D4 Teknik Informatika).
    • Email (mis. ahmad.rizki@kampus.ac.id).
    • Telepon Mahasiswa (mis. 081234567890).
    • Kontak Darurat (mis. 081298765432) — ditandai merah.
- Tombol aksi cepat:
    • "Hubungi Mahasiswa" (hijau, link tel:) — ikon Phone.
    • "Hubungi Darurat" (merah, link tel:) — ikon Phone.


================================================================
HALAMAN 11. LOG AKTIVITAS  (tab "Log Aktivitas" di Admin)
================================================================

Kartu Atas:
- Judul "Log Aktivitas".
- Deskripsi: "Semua aksi terhadap alert dan identitas mahasiswa
  tercatat otomatis untuk transparansi dan audit."

Daftar Log (contoh data, tiap item kartu):
- Identitas Dibuka (biru) — KRITIS — Dr. Sarah Wijaya — STD-2024-001
  Detail: 'Identitas dibuka karena deteksi kata kunci kritis:
  "tidak ingin hidup"'.
- Alert Dibuat (kuning) — KRITIS — Sistem — STD-2024-002
  Detail: 'Alert otomatis: kata kunci "bunuh diri" terdeteksi'.
- Identitas Dibuka (biru) — WASPADA — Dr. Budi Hartono — STD-2024-003
  Detail: "Identitas dibuka untuk tindak lanjut konseling".
- Alert Ditindaklanjuti (hijau) — KRITIS — Dr. Sarah Wijaya
  — STD-2024-004
  Detail: "Mahasiswa telah dihubungi dan dijadwalkan konseling".
- Alert Dibuat (kuning) — WASPADA — Sistem — STD-2024-005
  Detail: "Alert otomatis: pola mood negatif selama 7 hari berturut-turut".
- Identitas Dibuka (biru) — KRITIS — Dr. Budi Hartono — STD-2024-006
  Detail: "Identitas dibuka karena skor self-check kritis 3 hari
  berturut-turut".

Tiap kartu menampilkan:
- Ikon aksi (Eye / AlertTriangle / CheckCircle).
- Label aksi (Identitas Dibuka / Alert Dibuat / Alert Ditindaklanjuti).
- Badge severitas KRITIS (merah) atau WASPADA (kuning).
- Waktu (ikon Clock).
- Detail singkat aksi.
- ID mahasiswa & "Oleh: [nama admin/Sistem]".

Kartu "Ringkasan Aktivitas" (bawah):
- 3 — Identitas dibuka hari ini (biru).
- 2 — Alert aktif (kuning).
- 1 — Kasus ditindaklanjuti (hijau).


================================================================
SELESAI
================================================================
