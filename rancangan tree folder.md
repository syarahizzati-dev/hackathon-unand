CAMPUS-E/
│
├── fastapi/
│   ├── app/
│   │   ├── __init__.py
│   │   ├── main.py
│   │   │
│   │   ├── api/
│   │   │   ├── __init__.py
│   │   │   └── routes.py
│   │   │
│   │   ├── core/
│   │   │   ├── __init__.py
│   │   │   └── config.py
│   │   │
│   │   └── services/
│   │       ├── __init__.py
│   │       ├── predictor.py
│   │       └── anonymizer.py
│   │
│   ├── ai_model/
│   │   └── campus_e_indobert_model/
│   │       ├── config.json
│   │       ├── model.safetensors
│   │       ├── special_tokens_map.json
│   │       ├── tokenizer_config.json
│   │       └── vocab.txt
│   │
│   ├── .env
│   ├── requirements.txt
│   └── run.py
│
├── laravel/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Auth/
│   │   │   │   │   ├── AuthenticatedSessionController.php   ← Breeze: login mahasiswa
│   │   │   │   │   ├── RegisteredUserController.php         ← Breeze: register mahasiswa
│   │   │   │   │   ├── PasswordResetLinkController.php      ← Breeze: bawaan
│   │   │   │   │   ├── NewPasswordController.php            ← Breeze: bawaan
│   │   │   │   │   ├── ConfirmablePasswordController.php    ← Breeze: bawaan
│   │   │   │   │   ├── EmailVerificationController.php      ← Breeze: bawaan
│   │   │   │   │   └── AdminLoginController.php             ← CUSTOM: login admin terpisah
│   │   │   │   │
│   │   │   │   ├── Admin/
│   │   │   │   │   ├── IdentityController.php               ← CUSTOM: buka identitas darurat
│   │   │   │   │   └── LogController.php                    ← CUSTOM: log aktivitas
│   │   │   │   │
│   │   │   │   └── ProfileController.php                    ← Breeze: bawaan
│   │   │   │
│   │   │   └── Middleware/
│   │   │       ├── IsAdmin.php                              ← CUSTOM
│   │   │       └── IsMahasiswa.php                          ← CUSTOM
│   │   │
│   │   ├── Livewire/
│   │   │   ├── Mahasiswa/
│   │   │   │   ├── SelfCheckBanner.php                      ← Halaman 4: banner
│   │   │   │   ├── SelfCheckForm.php                        ← Halaman 5: 5 pertanyaan
│   │   │   │   ├── MoodTracker.php                          ← Halaman 6: kalender
│   │   │   │   ├── BukuHarian.php                           ← Halaman 7: curhat + AI
│   │   │   │   └── TukarPikiran.php                         ← Halaman 8: forum anonim
│   │   │   │
│   │   │   └── Admin/
│   │   │       ├── Dashboard.php                            ← Halaman 9: KPI
│   │   │       ├── AlertPanel.php                           ← Halaman 10: alert krisis
│   │   │       └── ActivityLog.php                          ← Halaman 11: log
│   │   │
│   │   ├── Models/
│   │   │   ├── User.php                                     ← Breeze: dimodifikasi tambah kolom
│   │   │   ├── SelfCheck.php
│   │   │   ├── BukuHarian.php
│   │   │   ├── ForumPost.php
│   │   │   ├── ForumReply.php
│   │   │   ├── ForumLike.php
│   │   │   ├── Alert.php
│   │   │   └── ActivityLog.php
│   │   │
│   │   └── Services/
│   │       └── AIService.php
│   │
│   ├── database/
│   │   ├── migrations/
│   │   │   ├── 0001_01_01_000000_create_users_table.php         ← Breeze: dimodifikasi
│   │   │   ├── 0001_01_01_000001_create_cache_table.php         ← Breeze: bawaan
│   │   │   ├── 0001_01_01_000002_create_jobs_table.php          ← Breeze: bawaan
│   │   │   ├── xxxx_xx_xx_create_self_checks_table.php          ← CUSTOM
│   │   │   ├── xxxx_xx_xx_create_buku_harian_table.php          ← CUSTOM
│   │   │   ├── xxxx_xx_xx_create_forum_posts_table.php          ← CUSTOM
│   │   │   ├── xxxx_xx_xx_create_forum_replies_table.php        ← CUSTOM
│   │   │   ├── xxxx_xx_xx_create_forum_likes_table.php          ← CUSTOM
│   │   │   ├── xxxx_xx_xx_create_alerts_table.php               ← CUSTOM
│   │   │   └── xxxx_xx_xx_create_activity_logs_table.php        ← CUSTOM
│   │   │
│   │   └── seeders/
│   │       ├── DatabaseSeeder.php
│   │       ├── AdminSeeder.php
│   │       └── DummyMahasiswaSeeder.php
│   │
│   ├── resources/
│   │   ├── css/
│   │   │   └── app.css                                          ← Breeze: Tailwind entry
│   │   │
│   │   ├── js/
│   │   │   └── app.js                                           ← Breeze: Alpine.js entry
│   │   │
│   │   └── views/
│   │       ├── layouts/
│   │       │   ├── app.blade.php                                ← Breeze: layout utama
│   │       │   ├── guest.blade.php                              ← Breeze: layout halaman auth
│   │       │   ├── navigation.blade.php                         ← Breeze: navbar bawaan (tidak dipakai)
│   │       │   ├── mahasiswa.blade.php                          ← CUSTOM: layout + tab navigasi mahasiswa
│   │       │   └── admin.blade.php                              ← CUSTOM: layout + tab navigasi admin
│   │       │
│   │       ├── auth/
│   │       │   ├── login.blade.php                              ← Breeze: DIGANTI custom login mahasiswa
│   │       │   ├── register.blade.php                           ← Breeze: DIGANTI custom register
│   │       │   ├── admin-login.blade.php                        ← CUSTOM: login admin
│   │       │   ├── forgot-password.blade.php                    ← Breeze: bawaan
│   │       │   ├── reset-password.blade.php                     ← Breeze: bawaan
│   │       │   ├── verify-email.blade.php                       ← Breeze: bawaan
│   │       │   └── confirm-password.blade.php                   ← Breeze: bawaan
│   │       │
│   │       ├── mahasiswa/
│   │       │   ├── dashboard.blade.php                          ← Halaman 4: wrapper tab
│   │       │   ├── mood.blade.php                               ← Halaman 6
│   │       │   ├── buku-harian.blade.php                        ← Halaman 7
│   │       │   └── tukar-pikiran.blade.php                      ← Halaman 8
│   │       │
│   │       ├── admin/
│   │       │   ├── dashboard.blade.php                          ← Halaman 9
│   │       │   ├── alert.blade.php                              ← Halaman 10
│   │       │   └── log.blade.php                                ← Halaman 11
│   │       │
│   │       ├── profile/
│   │       │   └── edit.blade.php                               ← Breeze: bawaan (tidak dipakai)
│   │       │
│   │       ├── livewire/
│   │       │   ├── mahasiswa/
│   │       │   │   ├── self-check-banner.blade.php
│   │       │   │   ├── self-check-form.blade.php
│   │       │   │   ├── mood-tracker.blade.php
│   │       │   │   ├── buku-harian.blade.php
│   │       │   │   └── tukar-pikiran.blade.php
│   │       │   │
│   │       │   └── admin/
│   │       │       ├── dashboard.blade.php
│   │       │       ├── alert-panel.blade.php
│   │       │       └── activity-log.blade.php
│   │       │
│   │       └── dashboard.blade.php                              ← Breeze: TIDAK DIPAKAI (diganti mahasiswa/dashboard)
│   │
│   ├── routes/
│   │   ├── web.php                                              ← CUSTOM: semua route
│   │   └── auth.php                                             ← Breeze: route auth bawaan (dimodifikasi)
│   │
│   ├── config/
│   │   └── services.php                                         ← tambah FASTAPI_URL
│   │
│   ├── .env
│   ├── composer.json
│   ├── package.json
│   ├── tailwind.config.js
│   └── vite.config.js
│
└── README.md