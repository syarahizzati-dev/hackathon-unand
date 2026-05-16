<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SelfCheck;
use App\Models\BukuHarian;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\ForumLike;
use App\Models\Alert;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyMahasiswaSeeder extends Seeder
{
    /**
     * Seed mahasiswa dummy beserta aktivitas lengkap.
     */
    public function run(): void
    {
        // ═══════════════════════════════════════════════════════════
        // 1. MAHASISWA DUMMY
        // ═══════════════════════════════════════════════════════════

        $mahasiswaData = [
            [
                'nama' => 'Ahmad Rizki Pratama',
                'nim' => '2024010001',
                'email' => 'ahmad.rizki@kampus.ac.id',
                'no_telepon' => '081234567890',
                'jurusan' => 'Teknologi Informasi',
                'program_studi' => 'D4 Teknik Informatika',
                'kontak_darurat' => '081298765432',
                'username_anonim' => 'BintangMalam_42',
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'nim' => '2024010002',
                'email' => 'siti.nurhaliza@kampus.ac.id',
                'no_telepon' => '081345678901',
                'jurusan' => 'Manajemen',
                'program_studi' => 'S1 Manajemen Bisnis',
                'kontak_darurat' => '081387654321',
                'username_anonim' => 'LangitBiru_17',
            ],
            [
                'nama' => 'Budi Santoso',
                'nim' => '2024010003',
                'email' => 'budi.santoso@kampus.ac.id',
                'no_telepon' => '081456789012',
                'jurusan' => 'Psikologi',
                'program_studi' => 'S1 Psikologi',
                'kontak_darurat' => '081476543210',
                'username_anonim' => 'HujanSore_88',
            ],
            [
                'nama' => 'Dewi Lestari',
                'nim' => '2024010004',
                'email' => 'dewi.lestari@kampus.ac.id',
                'no_telepon' => '081567890123',
                'jurusan' => 'Teknologi Informasi',
                'program_studi' => 'D3 Sistem Informasi',
                'kontak_darurat' => '081565432109',
                'username_anonim' => 'MelatiBening_55',
            ],
            [
                'nama' => 'Reza Firmansyah',
                'nim' => '2024010005',
                'email' => 'reza.firmansyah@kampus.ac.id',
                'no_telepon' => '081678901234',
                'jurusan' => 'Teknik Elektro',
                'program_studi' => 'D4 Teknik Elektro',
                'kontak_darurat' => '081654321098',
                'username_anonim' => 'ElangJingga_23',
            ],
            [
                'nama' => 'Putri Amelia',
                'nim' => '2024010006',
                'email' => 'putri.amelia@kampus.ac.id',
                'no_telepon' => '081789012345',
                'jurusan' => 'Manajemen',
                'program_studi' => 'S1 Akuntansi',
                'kontak_darurat' => '081743210987',
                'username_anonim' => 'PerakSenja_71',
            ],
            [
                'nama' => 'Dimas Prasetyo',
                'nim' => '2024010007',
                'email' => 'dimas.prasetyo@kampus.ac.id',
                'no_telepon' => '081890123456',
                'jurusan' => 'Psikologi',
                'program_studi' => 'S1 Psikologi',
                'kontak_darurat' => '081832109876',
                'username_anonim' => 'KabutPagi_39',
            ],
            [
                'nama' => 'Anisa Rahma',
                'nim' => '2024010008',
                'email' => 'anisa.rahma@kampus.ac.id',
                'no_telepon' => '081901234567',
                'jurusan' => 'Teknologi Informasi',
                'program_studi' => 'D4 Teknik Informatika',
                'kontak_darurat' => '081921098765',
                'username_anonim' => 'AnginLaut_66',
            ],
        ];

        $users = [];
        foreach ($mahasiswaData as $data) {
            $users[] = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => Hash::make('mahasiswa123'),
                    'is_admin' => false,
                ])
            );
        }

        // ═══════════════════════════════════════════════════════════
        // 2. SELF-CHECKS (variasi skor)
        // ═══════════════════════════════════════════════════════════

        $selfCheckData = [
            // Ahmad — baik
            [$users[0]->id, 5, 4, 5, 4, 5, 23, 0, 'LOW',  Carbon::now()->subDays(2)],
            [$users[0]->id, 4, 4, 4, 3, 4, 19, 1, 'LOW',  Carbon::now()->subDays(1)],
            [$users[0]->id, 5, 5, 4, 5, 4, 23, 0, 'LOW',  Carbon::now()],
            // Siti — waspada
            [$users[1]->id, 3, 2, 3, 3, 3, 14, 2, 'MEDIUM', Carbon::now()->subDays(1)],
            [$users[1]->id, 2, 3, 2, 3, 2, 12, 2, 'MEDIUM', Carbon::now()],
            // Budi — kritis
            [$users[2]->id, 2, 1, 1, 2, 1, 7,  3, 'HIGH',  Carbon::now()->subDays(2)],
            [$users[2]->id, 1, 1, 2, 1, 1, 6,  4, 'CRITICAL', Carbon::now()->subDays(1)],
            [$users[2]->id, 1, 2, 1, 1, 2, 7,  3, 'HIGH',  Carbon::now()],
            // Dewi — baik
            [$users[3]->id, 4, 5, 4, 4, 5, 22, 0, 'LOW',  Carbon::now()],
            // Reza — waspada
            [$users[4]->id, 3, 2, 3, 2, 3, 13, 2, 'MEDIUM', Carbon::now()],
            // Putri — baik
            [$users[5]->id, 5, 4, 5, 5, 4, 23, 0, 'LOW',  Carbon::now()],
            // Dimas — kritis
            [$users[6]->id, 1, 1, 1, 2, 1, 6,  4, 'CRITICAL', Carbon::now()],
            // Anisa — waspada
            [$users[7]->id, 3, 3, 2, 3, 2, 13, 2, 'MEDIUM', Carbon::now()],
        ];

        foreach ($selfCheckData as $sc) {
            $labelMap = [5=>'Sangat Baik',4=>'Baik',3=>'Biasa',2=>'Kurang',1=>'Buruk'];
            $teks = "Tidur: {$labelMap[$sc[1]]}. Mood: {$labelMap[$sc[2]]}. Cemas: {$labelMap[$sc[3]]}. Motivasi: {$labelMap[$sc[4]]}. Kesepian: {$labelMap[$sc[5]]}.";
            SelfCheck::create([
                'user_id'    => $sc[0],
                'jawaban_1'  => $sc[1],
                'jawaban_2'  => $sc[2],
                'jawaban_3'  => $sc[3],
                'jawaban_4'  => $sc[4],
                'jawaban_5'  => $sc[5],
                'skor_total' => $sc[6],
                'teks_gabung' => $teks,
                'label'      => $sc[7],
                'risk_level' => $sc[8],
                'confidence' => 0.85,
                'created_at' => $sc[9],
                'updated_at' => $sc[9],
            ]);
        }

        // ═══════════════════════════════════════════════════════════
        // 3. BUKU HARIAN
        // ═══════════════════════════════════════════════════════════

        $diaryEntries = [
            [$users[0]->id, 'Hari ini cukup menyenangkan, tugas kelompok berjalan lancar dan teman-teman sangat mendukung. Senang sekali!', 'Senang mendengar kamu baik-baik saja! Terus jaga kesehatan mentalmu ya. 😊', ['Jalan-jalan santai di taman kampus', 'Tulis 3 hal yang kamu syukuri hari ini'], 0, 'LOW', true, Carbon::now()->subDays(1)],
            [$users[1]->id, 'Entah kenapa akhir-akhir ini aku merasa cemas terus. Tugas menumpuk dan aku merasa tidak mampu menyelesaikan semuanya. Rasanya tertekan.', 'Terima kasih sudah mau berbagi. Perasaanmu valid dan penting. Kamu tidak sendirian. 💙', ['Luangkan waktu istirahat 15 menit', 'Coba teknik pernapasan 4-7-8'], 2, 'MEDIUM', true, Carbon::now()->subDays(1)],
            [$users[2]->id, 'Saya merasa sangat lelah dengan semuanya. Tidak ingin hidup seperti ini lagi. Rasanya tidak ada yang peduli dengan saya.', 'Kami sangat khawatir dengan kondisimu. Tolong hubungi orang terdekatmu atau konselor kampus sekarang. Kamu berharga. 💙', ['Segera hubungi konselor kampus', 'Hubungi hotline kesehatan mental: 119 ext. 8'], 4, 'CRITICAL', true, Carbon::now()->subHours(3)],
            [$users[3]->id, 'Hari ini aku berhasil menyelesaikan presentasi dengan baik! Dosennya bilang bagus sekali. Senang banget 🎉', 'Bagus sekali! Teruslah menjaga keseimbangan hidupmu. 🌟', ['Rayakan pencapaianmu dengan aktivitas menyenangkan', 'Bagikan keberhasilanmu dengan orang terdekat'], 0, 'LOW', true, Carbon::now()],
            [$users[6]->id, 'Aku tidak berguna untuk siapa-siapa. Mungkin lebih baik jika aku tidak ada. Semua orang akan lebih baik tanpa aku.', 'Hidupmu sangat berharga. Tolong jangan ragu untuk menghubungi layanan krisis atau orang yang kamu percaya. ❤️', ['Segera hubungi konselor kampus', 'Jangan sendirian — hubungi keluarga'], 4, 'CRITICAL', true, Carbon::now()->subHours(5)],
        ];

        foreach ($diaryEntries as $de) {
            BukuHarian::create([
                'user_id'     => $de[0],
                'isi'         => $de[1],
                'ai_reply'    => $de[2],
                'ai_saran'    => $de[3],
                'label'       => $de[4],
                'risk_level'  => $de[5],
                'is_analyzed' => $de[6],
                'confidence'  => 0.92,
                'created_at'  => $de[7],
                'updated_at'  => $de[7],
            ]);
        }

        // ═══════════════════════════════════════════════════════════
        // 4. FORUM POSTS + REPLIES + LIKES
        // ═══════════════════════════════════════════════════════════

        $post1 = ForumPost::create([
            'user_id'    => $users[0]->id,
            'konten'     => 'Ada yang punya tips buat mengatasi insomnia? Akhir-akhir ini susah banget tidur karena pikiran terus berputar 😩',
            'label'      => 1,
            'risk_level' => 'LOW',
            'is_hidden'  => false,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        $post2 = ForumPost::create([
            'user_id'    => $users[1]->id,
            'konten'     => 'Rasanya kuliah semester ini berat banget. Tapi aku coba terus semangat. Ada yang mau jadi teman curhat?',
            'label'      => 1,
            'risk_level' => 'LOW',
            'is_hidden'  => false,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        $post3 = ForumPost::create([
            'user_id'    => $users[3]->id,
            'konten'     => 'Mau share, hari ini aku berhasil presentasi tanpa nervous! Dulu aku social anxiety parah, tapi pelan-pelan bisa. Kalian juga pasti bisa! 💪',
            'label'      => 0,
            'risk_level' => 'LOW',
            'is_hidden'  => false,
            'created_at' => Carbon::now()->subHours(8),
        ]);

        $post4 = ForumPost::create([
            'user_id'    => $users[7]->id,
            'konten'     => 'Kadang aku merasa putus asa dengan nilai-nilaiku. Tapi aku berusaha ingat bahwa nilai bukan segalanya. Tetap semangat ya teman-teman.',
            'label'      => 2,
            'risk_level' => 'MEDIUM',
            'is_hidden'  => false,
            'created_at' => Carbon::now()->subHours(4),
        ]);

        // Replies
        ForumReply::create(['post_id' => $post1->id, 'user_id' => $users[3]->id, 'konten' => 'Coba matikan HP 30 menit sebelum tidur, bantu banget buat aku!', 'created_at' => Carbon::now()->subDays(2)->addHours(1)]);
        ForumReply::create(['post_id' => $post1->id, 'user_id' => $users[5]->id, 'konten' => 'Aku biasanya dengerin white noise. Ada app Calm yang gratis 🎧', 'created_at' => Carbon::now()->subDays(2)->addHours(2)]);
        ForumReply::create(['post_id' => $post2->id, 'user_id' => $users[0]->id, 'konten' => 'Semangat! Kamu nggak sendirian kok. DM aku kalau mau ngobrol 💙', 'created_at' => Carbon::now()->subDays(1)->addHours(1)]);
        ForumReply::create(['post_id' => $post3->id, 'user_id' => $users[7]->id, 'konten' => 'Inspiratif banget! Aku juga lagi belajar mengatasi anxiety. Terima kasih sharingnya!', 'created_at' => Carbon::now()->subHours(6)]);
        ForumReply::create(['post_id' => $post4->id, 'user_id' => $users[4]->id, 'konten' => 'Bener banget, proses lebih penting dari hasil. Semangat! 🌟', 'created_at' => Carbon::now()->subHours(2)]);

        // Likes
        ForumLike::create(['post_id' => $post1->id, 'user_id' => $users[1]->id]);
        ForumLike::create(['post_id' => $post1->id, 'user_id' => $users[3]->id]);
        ForumLike::create(['post_id' => $post2->id, 'user_id' => $users[0]->id]);
        ForumLike::create(['post_id' => $post2->id, 'user_id' => $users[4]->id]);
        ForumLike::create(['post_id' => $post2->id, 'user_id' => $users[7]->id]);
        ForumLike::create(['post_id' => $post3->id, 'user_id' => $users[0]->id]);
        ForumLike::create(['post_id' => $post3->id, 'user_id' => $users[1]->id]);
        ForumLike::create(['post_id' => $post3->id, 'user_id' => $users[5]->id]);
        ForumLike::create(['post_id' => $post3->id, 'user_id' => $users[7]->id]);
        ForumLike::create(['post_id' => $post4->id, 'user_id' => $users[3]->id]);
        ForumLike::create(['post_id' => $post4->id, 'user_id' => $users[5]->id]);

        // ═══════════════════════════════════════════════════════════
        // 5. ALERTS (KRITIS + WASPADA)
        // ═══════════════════════════════════════════════════════════

        $admin1 = User::where('email', 'admin@kampus.ac.id')->first();

        // Alert 1 — Budi (KRITIS dari buku harian)
        $alert1 = Alert::create([
            'user_id'       => $users[2]->id,
            'sumber'        => 'buku_harian',
            'sumber_id'     => 3,
            'label'         => 4,
            'risk_level'    => 'CRITICAL',
            'confidence'    => 0.95,
            'kata_kunci'    => ['tidak ingin hidup', 'tidak ada yang peduli'],
            'cuplikan_teks' => 'Saya merasa sangat lelah dengan semuanya. Tidak ingin hidup seperti ini lagi...',
            'is_handled'    => false,
            'created_at'    => Carbon::now()->subHours(3),
        ]);

        // Alert 2 — Dimas (KRITIS dari buku harian)
        $alert2 = Alert::create([
            'user_id'       => $users[6]->id,
            'sumber'        => 'buku_harian',
            'sumber_id'     => 5,
            'label'         => 4,
            'risk_level'    => 'CRITICAL',
            'confidence'    => 0.93,
            'kata_kunci'    => ['tidak berguna', 'lebih baik tidak ada'],
            'cuplikan_teks' => 'Aku tidak berguna untuk siapa-siapa. Mungkin lebih baik jika aku tidak ada.',
            'is_handled'    => false,
            'created_at'    => Carbon::now()->subHours(5),
        ]);

        // Alert 3 — Anisa (WASPADA dari self-check)
        $alert3 = Alert::create([
            'user_id'       => $users[7]->id,
            'sumber'        => 'self_check',
            'sumber_id'     => 13,
            'label'         => 2,
            'risk_level'    => 'MEDIUM',
            'confidence'    => 0.78,
            'kata_kunci'    => ['putus asa', 'cemas'],
            'cuplikan_teks' => 'Kadang aku merasa putus asa dengan nilai-nilaiku...',
            'is_handled'    => false,
            'created_at'    => Carbon::now()->subHours(4),
        ]);

        // ═══════════════════════════════════════════════════════════
        // 6. ACTIVITY LOGS
        // ═══════════════════════════════════════════════════════════

        ActivityLog::create([
            'aksi'           => 'alert_dibuat',
            'severity'       => 'kritis',
            'alert_id'       => $alert1->id,
            'target_user_id' => $users[2]->id,
            'actor_label'    => 'Sistem',
            'detail'         => 'Alert otomatis: kata kunci "tidak ingin hidup" terdeteksi dari buku harian',
            'created_at'     => Carbon::now()->subHours(3),
        ]);

        ActivityLog::create([
            'aksi'           => 'alert_dibuat',
            'severity'       => 'kritis',
            'alert_id'       => $alert2->id,
            'target_user_id' => $users[6]->id,
            'actor_label'    => 'Sistem',
            'detail'         => 'Alert otomatis: kata kunci "bunuh diri", "tidak berguna" terdeteksi',
            'created_at'     => Carbon::now()->subHours(5),
        ]);

        ActivityLog::create([
            'aksi'           => 'alert_dibuat',
            'severity'       => 'waspada',
            'alert_id'       => $alert3->id,
            'target_user_id' => $users[7]->id,
            'actor_label'    => 'Sistem',
            'detail'         => 'Alert otomatis: pola mood negatif dari self-check',
            'created_at'     => Carbon::now()->subHours(4),
        ]);

        // Simulasi aktivitas admin
        if ($admin1) {
            ActivityLog::create([
                'aksi'           => 'identitas_dibuka',
                'severity'       => 'kritis',
                'alert_id'       => $alert1->id,
                'target_user_id' => $users[2]->id,
                'actor_id'       => $admin1->id,
                'actor_label'    => $admin1->nama,
                'detail'         => 'Identitas dibuka karena deteksi kata kunci kritis: "tidak ingin hidup"',
                'created_at'     => Carbon::now()->subHours(2),
            ]);

            ActivityLog::create([
                'aksi'           => 'alert_ditindaklanjuti',
                'severity'       => 'kritis',
                'alert_id'       => $alert1->id,
                'target_user_id' => $users[2]->id,
                'actor_id'       => $admin1->id,
                'actor_label'    => $admin1->nama,
                'detail'         => 'Mahasiswa telah dihubungi dan dijadwalkan konseling',
                'created_at'     => Carbon::now()->subHours(1),
            ]);

            // Tandai alert 1 sebagai ditangani
            $alert1->update([
                'is_handled'      => true,
                'handled_by'      => $admin1->id,
                'handled_at'      => Carbon::now()->subHours(1),
                'identity_opened' => true,
                'opened_by'       => $admin1->id,
                'opened_at'       => Carbon::now()->subHours(2),
            ]);
        }
    }
}
