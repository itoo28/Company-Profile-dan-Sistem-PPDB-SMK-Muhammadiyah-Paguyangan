<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\Ekstrakurikuler;
use App\Models\Fasilitas;
use App\Models\Galeri;
use App\Models\InformasiSekolah;
use App\Models\JenisBerkas;
use App\Models\PengaturanSistem;
use App\Models\Pengumuman;
use App\Models\Prestasi;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pengguna Staf
        $superAdmin = User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator SMK',
                'email' => 'superadmin@smkmuhpaguyangan.sch.id',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'is_aktif' => true,
            ]
        );

        $adminPiket = User::updateOrCreate(
            ['username' => 'adminpiket'],
            [
                'name' => 'Petugas Piket PPDB',
                'email' => 'piket@smkmuhpaguyangan.sch.id',
                'password' => Hash::make('piket123'),
                'role' => 'admin_piket',
                'is_aktif' => true,
            ]
        );

        // 2. Tahun Ajaran Aktif
        $tahunAjaran = TahunAjaran::updateOrCreate(
            ['nama' => '2026/2027'],
            ['is_aktif' => true]
        );

        // 3. Pengaturan Sistem
        PengaturanSistem::updateOrCreate(
            ['id' => 1],
            [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'batas_waktu_berkas' => Carbon::now()->addDays(30),
                'max_file_size_mb' => 2,
                'nomor_urut_terakhir' => 0,
                'nama_sekolah' => 'SMK Muhammadiyah Paguyangan',
                'slogan' => 'Pusat Keunggulan Vokasi Islami Berkarakter dan Siap Kerja',
                'alamat' => 'Jl. Raya Paguyangan, Dukuh Benda, Paguyangan, Brebes, Jawa Tengah 52276',
                'telepon' => '(0289) 430123',
                'whatsapp' => '0857-2345-6789',
                'email' => 'info@smkmuhpaguyangan.sch.id',
                'jam_operasional' => 'Senin - Jumat: 07.00 - 15.30 WIB | Sabtu: 07.00 - 13.00 WIB',
                'maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.3375836912384!2d109.029812!3d-7.247656!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f9a0c3b8a3e75%3A0x6b189ff4c28f6e80!2sSMK%20Muhammadiyah%20Paguyangan!5e0!3m2!1sid!2sid!4v1700000000000',
            ]
        );

        // 4. Master Jenis Berkas Wajib & Opsional
        $jenisBerkasList = [
            [
                'nama' => 'Kartu Keluarga (KK)',
                'is_wajib' => true,
                'deskripsi' => 'Salinan resmi Kartu Keluarga terbaru yang masih berlaku (format PDF atau JPG).',
                'urutan' => 1,
            ],
            [
                'nama' => 'Akta Kelahiran',
                'is_wajib' => true,
                'deskripsi' => 'Scan/foto dokumen Akta Kelahiran asli calon siswa.',
                'urutan' => 2,
            ],
            [
                'nama' => 'Ijazah / Surat Keterangan Lulus (SKL)',
                'is_wajib' => true,
                'deskripsi' => 'Scan Ijazah SMP/MTs atau Surat Keterangan Lulus resmi dari sekolah asal.',
                'urutan' => 3,
            ],
            [
                'nama' => 'Surat Keterangan Sehat & Bebas Buta Warna',
                'is_wajib' => false,
                'deskripsi' => 'Surat keterangan dari Puskesmas atau dokter (khusus rekomendasi program keahlian teknik).',
                'urutan' => 4,
            ],
        ];

        foreach ($jenisBerkasList as $item) {
            JenisBerkas::updateOrCreate(['nama' => $item['nama']], $item);
        }

        // 5. Berita Terkini SMK Muhammadiyah Paguyangan
        $beritas = [
            [
                'judul' => 'Peresmian Gedung Belajar Baru SMK Muhammadiyah Paguyangan untuk Praktik Kejuruan',
                'slug' => 'peresmian-gedung-belajar-baru-smk-muhammadiyah-paguyangan',
                'ringkasan' => 'Gedung baru dua lantai resmi diresmikan untuk menampung bengkel praktik otomotif dan laboratorium jaringan komputer dengan standar industri.',
                'isi' => '<p>SMK Muhammadiyah Paguyangan meresmikan penambahan fasilitas gedung pembelajaran baru yang representatif di kawasan Paguyangan, Brebes. Gedung ini diperuntukkan bagi penguatan praktikum kejuruan siswa di bidang teknik dan teknologi informasi.</p><p>Acara peresmian dihadiri jajaran Pimpinan Cabang Muhammadiyah (PCM) Paguyangan, pengawas SMK Dinas Pendidikan Jawa Tengah, serta perwakilan mitra dunia industri. Kepala SMK Muhammadiyah Paguyangan menyampaikan bahwa fasilitas baru ini dirancang untuk menjawab tuntutan kurikulum berbasis industri yang menuntut keterampilan teknis secara nyata dan terukur.</p><p>Gedung baru ini menampung bengkel kerja berstandar bengkel resmi dan laboratorium komputer berkecepatan tinggi dengan perangkat jaringan mutakhir.</p>',
                'gambar_sampul' => 'images/gambargedungbaru.jpg',
                'tanggal_terbit' => Carbon::now()->subDays(2),
                'status' => 'terbit',
                'penulis' => 'Humas SMK Muhammadiyah Paguyangan',
                'dibuat_oleh' => $superAdmin->id,
                'views_count' => 148,
            ],
            [
                'judul' => 'Penerimaan Peserta Didik Baru (PPDB) Tahun Ajaran 2026/2027 Resmi Dibuka Melalui Sistem Daring',
                'slug' => 'ppdb-tahun-ajaran-2026-2027-resmi-dibuka',
                'ringkasan' => 'Calon siswa SMP/MTs dapat mendaftar langsung secara daring atau mengunjungi loket PPDB langsung di kampus sekolah.',
                'isi' => '<p>Panitia Penerimaan Peserta Didik Baru (PPDB) SMK Muhammadiyah Paguyangan mengumumkan pembukaan pendaftaran peserta didik baru untuk tahun ajaran 2026/2027. Sistem pendaftaran tahun ini memudahkan calon siswa dan orang tua melalui formulir mandiri berbasis web.</p><p>Calon pendaftar cukup mengisi formulir data diri, memperoleh bukti nomor pendaftaran, lalu mengunggah berkas persyaratan seperti Kartu Keluarga, Akta Kelahiran, dan Ijazah/SKL melalui portal siswa.</p><p>Bagi calon siswa yang membutuhkan pendampingan, panitia loket PPDB sekolah siap membantu pendaftaran offline di kampus setiap hari kerja pukul 07.30 hingga 14.30 WIB.</p>',
                'gambar_sampul' => 'images/gambarutama2.jpeg',
                'tanggal_terbit' => Carbon::now()->subDays(5),
                'status' => 'terbit',
                'penulis' => 'Panitia PPDB',
                'dibuat_oleh' => $adminPiket->id,
                'views_count' => 312,
            ],
            [
                'judul' => 'Kunjungan Industri dan Uji Kompetensi Keahlian Bersama Mitra Industri Logam dan Otomotif',
                'slug' => 'kunjungan-industri-dan-uji-kompetensi-keahlian',
                'ringkasan' => 'Siswa tingkat akhir melaksanakan uji kompetensi terstandar yang diawasi langsung oleh asesor dari asosiasi industri manufaktur.',
                'isi' => '<p>Sebagai bagian dari komitmen peningkatan mutu lulusan, SMK Muhammadiyah Paguyangan kembali menyelenggarakan uji kompetensi keahlian bekerja sama dengan praktisi industri mitra. Kegiatan ini menguji kesiapan kerja siswa dalam bidang bongkar pasang mesin, kelistrikan kendaraan, serta konfigurasi jaringan server kantor.</p><p>Para peserta didik menunjukkan disiplin kerja dan kepatuhan prosedur keselamatan (K3) yang tinggi selama sesi asesmen berlangsung.</p>',
                'gambar_sampul' => 'images/gambargedungbaru.jpg',
                'tanggal_terbit' => Carbon::now()->subDays(12),
                'status' => 'terbit',
                'penulis' => 'Humas & BKK SMK',
                'dibuat_oleh' => $superAdmin->id,
                'views_count' => 205,
            ],
        ];

        foreach ($beritas as $beritaData) {
            Berita::updateOrCreate(['slug' => $beritaData['slug']], $beritaData);
        }

        // 6. Prestasi Siswa & Sekolah
        $prestasis = [
            [
                'nama_prestasi' => 'Juara 1 Lomba Keterampilan Siswa (LKS) Bidang Motorcycle Maintenance & Repair',
                'slug' => 'juara-1-lks-motorcycle-maintenance-kabupaten-brebes',
                'nama_peraih' => 'Faris Kurniawan (Siswa Kelas XII TBSM)',
                'tingkat' => 'Kabupaten Brebes',
                'peringkat' => 'Juara 1',
                'tanggal_perolehan' => Carbon::now()->subMonths(1),
                'deskripsi' => 'Meraih medali emas dalam kompetisi pemeliharaan dan perbaikan sepeda motor tingkat SMK se-Kabupaten Brebes dengan skor praktik presisi tertinggi.',
                'foto' => 'images/gambarutama2.jpeg',
                'status' => 'terbit',
                'dibuat_oleh' => $superAdmin->id,
            ],
            [
                'nama_prestasi' => 'Juara 2 Kompetisi Jaringan Komputer dan Server Tingkat Karesidenan Pekalongan',
                'slug' => 'juara-2-kompetisi-jaringan-komputer-karesidenan-pekalongan',
                'nama_peraih' => 'Tim IT SMK Muhammadiyah Paguyangan (Dimas & Aditya)',
                'tingkat' => 'Karesidenan Pekalongan',
                'peringkat' => 'Juara 2',
                'tanggal_perolehan' => Carbon::now()->subMonths(3),
                'deskripsi' => 'Berhasil mengonfigurasi arsitektur jaringan VLAN dan pertahanan firewall Linux dalam waktu tercepat pada ajang temu vokasi SMK se-Karesidenan.',
                'foto' => 'images/gambargedungbaru.jpg',
                'status' => 'terbit',
                'dibuat_oleh' => $adminPiket->id,
            ],
            [
                'nama_prestasi' => 'Medali Emas Kejuaraan Pencak Silat Tapak Suci Pelajar Jawa Tengah',
                'slug' => 'medali-emas-pencak-silat-tapak-suci-pelajar-jawa-tengah',
                'nama_peraih' => 'Rizky Alamsyah (Kelas XI)',
                'tingkat' => 'Provinsi Jawa Tengah',
                'peringkat' => 'Medali Emas (Juara 1 Kelas C Putra)',
                'tanggal_perolehan' => Carbon::now()->subMonths(4),
                'deskripsi' => 'Mengukir prestasi membanggakan pada turnamen kejuaraan daerah Tapak Suci Putera Muhammadiyah tingkat pelajar antar-kabupaten se-Jawa Tengah.',
                'foto' => 'images/gambarutama2.jpeg',
                'status' => 'terbit',
                'dibuat_oleh' => $superAdmin->id,
            ],
        ];

        foreach ($prestasis as $prestasiData) {
            Prestasi::updateOrCreate(['slug' => $prestasiData['slug']], $prestasiData);
        }

        // 7. Pengumuman
        $pengumumans = [
            [
                'judul' => 'Jadwal Layanan Verifikasi Berkas Fisik dan Pengambilan Seragam Siswa Baru Gelombang 1',
                'slug' => 'jadwal-verifikasi-berkas-dan-pengambilan-seragam-gelombang-1',
                'isi' => 'Diberitahukan kepada seluruh pendaftar yang berkas online telah berstatus Disetujui, loket pelayanan seragam di ruang Tata Usaha dibuka setiap hari kerja pukul 08.00 - 14.00 WIB. Harap membawa lembar bukti pendaftaran yang dicetak dari portal siswa.',
                'tanggal' => Carbon::now()->subDays(1),
                'status' => 'terbit',
                'file_lampiran' => null,
            ],
            [
                'judul' => 'Sosialisasi Tata Tertib dan Persiapan Masa Pengenalan Lingkungan Sekolah (MPLS)',
                'slug' => 'sosialisasi-tata-tertib-mpls',
                'isi' => 'Rangkaian kegiatan pembekalan awal peserta didik baru berbasis penguatan karakter Islami, kedisiplinan vokasi, dan pengenalan budaya kerja industri akan dilaksanakan menjelang awal tahun ajaran baru.',
                'tanggal' => Carbon::now()->subDays(7),
                'status' => 'terbit',
                'file_lampiran' => null,
            ],
        ];

        foreach ($pengumumans as $pengumumanData) {
            Pengumuman::updateOrCreate(['slug' => $pengumumanData['slug']], $pengumumanData);
        }

        // 8. Fasilitas Sekolah
        $fasilitasList = [
            [
                'nama' => 'Gedung Pembelajaran Baru Kampus 2',
                'kategori' => 'Ruang Belajar',
                'deskripsi' => 'Gedung modern bertingkat yang menampung ruang kelas nyaman, dilengkapi proyektor multimedia, ventilasi alami yang sejuk, dan pencahayaan optimal.',
                'foto' => 'images/gambargedungbaru.jpg',
                'urutan' => 1,
            ],
            [
                'nama' => 'Bengkel Teknik Bisnis Sepeda Motor (TBSM)',
                'kategori' => 'Bengkel & Laboratorium',
                'deskripsi' => 'Bengkel praktik kejuruan otomotif lengkap dengan bike lift hidrolik, fuel injection tester, exhaust gas analyzer, dan perangkat perkakas mekanik berstandar bengkel resmi.',
                'foto' => 'images/gambargedungbaru.jpg',
                'urutan' => 2,
            ],
            [
                'nama' => 'Laboratorium Komputer & Jaringan (TJKT)',
                'kategori' => 'Bengkel & Laboratorium',
                'deskripsi' => 'Lab komputer dengan puluhan workstation berkinerja tinggi, rack server terpusat, perangkat routing Mikrotik, serta koneksi serat optik berkecepatan tinggi.',
                'foto' => 'images/gambarutama2.jpeg',
                'urutan' => 3,
            ],
            [
                'nama' => 'Perpustakaan & Ruang Baca Digital',
                'kategori' => 'Fasilitas Penunjang',
                'deskripsi' => 'Koleksi ribuan buku kejuruan, referensi keislaman, modul teknik, serta katalog komputer untuk menunjang literasi siswa dan tenaga pendidik.',
                'foto' => 'images/gambargedungbaru.jpg',
                'urutan' => 4,
            ],
            [
                'nama' => 'Musholla Sekolah As-Salam',
                'kategori' => 'Ibadah & Olahraga',
                'deskripsi' => 'Pusat pembinaan keagamaan siswa untuk salat dhuha berjamaah, salat dzuhur rutin, pengajian taruna melati, dan bimbingan membaca Al-Quran.',
                'foto' => 'images/gambargedungbaru.jpg',
                'urutan' => 5,
            ],
        ];

        foreach ($fasilitasList as $fasilitasData) {
            Fasilitas::updateOrCreate(['nama' => $fasilitasData['nama']], $fasilitasData);
        }

        // 9. Ekstrakurikuler
        $ekskulList = [
            [
                'nama' => 'Gerakan Kepanduan Hizbul Wathan (HW)',
                'kategori' => 'Kepanduan',
                'deskripsi' => 'Membentuk kedisiplinan, kemandirian, kepemimpinan, kepedulian sosial, dan kecintaan pada alam melalui kepanduan khas Muhammadiyah.',
                'pembina' => 'Kakak Pembina Qobilah HW',
                'jadwal' => 'Jumat, 14.00 - 16.30 WIB',
                'foto' => 'images/gambarutama2.jpeg',
            ],
            [
                'nama' => 'Tapak Suci Putera Muhammadiyah',
                'kategori' => 'Beladiri',
                'deskripsi' => 'Seni beladiri pencak silat dengan semboyan Dengan Iman dan Akhlak Saya Menjadi Kuat, melatih fisik prima dan integritas mental.',
                'pembina' => 'Pendekar Muda Tapak Suci',
                'jadwal' => 'Rabu & Sabtu, 15.30 - 17.30 WIB',
                'foto' => 'images/gambarutama2.jpeg',
            ],
            [
                'nama' => 'Ikatan Pelajar Muhammadiyah (PR IPM)',
                'kategori' => 'Keagamaan & Kepemimpinan',
                'deskripsi' => 'Organisasi kesiswaan resmi di sekolah untuk menumbuhkan kepemimpinan pelajar, kajian dakwah pemuda, dan kreativitas sosial sekolah.',
                'pembina' => 'Wakasek Kesiswaan',
                'jadwal' => 'Kondisional & Pekanan',
                'foto' => 'images/gambarutama2.jpeg',
            ],
            [
                'nama' => 'IT Club & Komunitas Robotika',
                'kategori' => 'Kejuruan/IT',
                'deskripsi' => 'Klub eksplorasi teknologi untuk pemrograman web dasar, jaringan komputer, mikrokontroler Arduino, dan perakitan PC gaming/desain.',
                'pembina' => 'Guru Produktif TJKT',
                'jadwal' => 'Kamis, 15.30 - 17.00 WIB',
                'foto' => 'images/gambarutama2.jpeg',
            ],
            [
                'nama' => 'Futsal & Bola Voli',
                'kategori' => 'Olahraga',
                'deskripsi' => 'Pengembangan minat bakat olahraga atletik untuk menjaga kebugaran jasmani dan partisipasi turnamen antarpelajar se-Brebes Selatan.',
                'pembina' => 'Guru Penjaskes',
                'jadwal' => 'Selasa & Jumat, 15.30 - 17.00 WIB',
                'foto' => 'images/gambargedungbaru.jpg',
            ],
        ];

        foreach ($ekskulList as $ekskulData) {
            Ekstrakurikuler::updateOrCreate(['nama' => $ekskulData['nama']], $ekskulData);
        }

        // 10. Galeri Sekolah
        $galeriList = [
            [
                'judul' => 'Aktivitas Praktik Siswa Otomotif di Bengkel Kerja',
                'kategori' => 'Praktikum',
                'foto' => 'images/gambarutama2.jpeg',
                'keterangan' => 'Siswa kelas XI melakukan analisis sistem kelistrikan injeksi sepeda motor di bawah panduan instruktur industri.',
                'tanggal' => Carbon::now()->subDays(10),
            ],
            [
                'judul' => 'Fasilitas Gedung Belajar Baru Kampus Paguyangan',
                'kategori' => 'Fasilitas',
                'foto' => 'images/gambargedungbaru.jpg',
                'keterangan' => 'Pemandangan tampak depan gedung belajar baru SMK Muhammadiyah Paguyangan yang rapi dan terawat.',
                'tanggal' => Carbon::now()->subDays(15),
            ],
            [
                'judul' => 'Upacara Hari Kemerdekaan dan Pelantikan Pengurus IPM',
                'kategori' => 'Kegiatan',
                'foto' => 'images/gambarutama2.jpeg',
                'keterangan' => 'Khitmatnya barisan peserta didik dan dewan guru mengenakan seragam almamater dalam apel sekolah.',
                'tanggal' => Carbon::now()->subMonths(1),
            ],
        ];

        foreach ($galeriList as $galeriData) {
            Galeri::updateOrCreate(['judul' => $galeriData['judul']], $galeriData);
        }

        // 11. Informasi Sekolah (Profil, Visi Misi, Program Keahlian, Sambutan)
        $informasiData = [
            [
                'key' => 'sambutan_kepala_sekolah',
                'judul' => 'Sambutan Kepala SMK Muhammadiyah Paguyangan',
                'konten' => '<p><em>Assalamu’alaikum Warahmatullahi Wabarakatuh,</em></p><p>Selamat datang di portal resmi SMK Muhammadiyah Paguyangan. Kami berkomitmen menyelenggarakan pendidikan kejuruan yang mengintegrasikan penguasaan kompetensi vokasi tingkat tinggi dengan pembentukan akhlak mulia berlandaskan nilai-nilai Islam Berkemajuan.</p><p>Melalui kurikulum yang bersinergi dengan dunia usaha dan dunia industri (DUDI), fasilitas bengkel dan laboratorium yang terus diperbarui, serta bimbingan para guru yang berdedikasi, kami berikhtiar mencetak generasi lulusan yang kompeten, mandiri, dan berkarakter.</p><p>Semoga media web ini memberikan keterbukaan informasi yang jelas dan memudahkan masyarakat dalam mengenal program pendidikan serta proses PPDB di sekolah kami.</p><p><em>Wassalamu’alaikum Warahmatullahi Wabarakatuh.</em></p>',
                'gambar' => 'images/gambarutama2.jpeg',
                'extra_data' => [
                    'nama_kepala_sekolah' => 'Drs. H. Bambang Suhartono, M.Pd.',
                    'nip' => 'NBM. 892.441',
                ],
            ],
            [
                'key' => 'visi',
                'judul' => 'Visi Sekolah',
                'konten' => 'Terwujudnya SMK Muhammadiyah Paguyangan sebagai pusat keunggulan pendidikan vokasi yang berakhlak mulia, unggul dalam kompetensi kejuruan, mandiri, dan berdaya saing di dunia industri nasional.',
                'gambar' => null,
                'extra_data' => null,
            ],
            [
                'key' => 'misi',
                'judul' => 'Misi Sekolah',
                'konten' => '<ol class="space-y-2"><li>Menanamkan nilai-nilai keislaman dan pembinaan akhlak karimah melalui keteladanan dan pembiasaan ibadah sehari-hari.</li><li>Menyelenggarakan proses pembelajaran kejuruan berbasis kompetensi industri dan teknologi terkini.</li><li>Menyediakan sarana bengkel praktikum dan laboratorium komputasi yang representatif dan berstandar K3.</li><li>Memperluas kemitraan strategis dengan dunia usaha dan industri untuk magang kerja, penyelarasan kurikulum, dan rekrutmen lulusan.</li><li>Mengembangkan jiwa wirausaha (entrepreneurship) mandiri dan kecakapan hidup bagi seluruh peserta didik.</li></ol>',
                'gambar' => null,
                'extra_data' => null,
            ],
            [
                'key' => 'tujuan',
                'judul' => 'Tujuan Pendidikan',
                'konten' => '<ul class="space-y-2"><li>Menghasilkan lulusan yang siap langsung bekerja di dunia industri atau melanjutkan ke perguruan tinggi.</li><li>Membekali peserta didik dengan sertifikasi kompetensi keahlian resmi yang diakui dunia kerja.</li><li>Membentuk karakter disiplin, jujur, pantang menyerah, dan memiliki etos kerja tinggi khas lulusan vokasi Muhammadiyah.</li></ul>',
                'gambar' => null,
                'extra_data' => null,
            ],
            [
                'key' => 'program_keahlian',
                'judul' => 'Program Keahlian Unggulan',
                'konten' => 'SMK Muhammadiyah Paguyangan menyelenggarakan program pendidikan kejuruan yang memiliki daya serap kerja tinggi di kawasan Brebes dan Jawa Tengah.',
                'gambar' => 'images/gambargedungbaru.jpg',
                'extra_data' => [
                    'jurusan' => [
                        [
                            'kode' => 'TBSM',
                            'nama' => 'Teknik Bisnis Sepeda Motor',
                            'deskripsi' => 'Mempelajari teknologi mesin injeksi, sistem transmisi otomatis, kelistrikan kendaraan roda dua, manajemen bengkel, serta penanganan servis berkala sesuai standar industri sepeda motor terkemuka.',
                            'prospek' => 'Mekanik bengkel resmi, teknisi servis mandiri, wirausaha perbengkelan, operator pabrik perakitan otomotif.',
                        ],
                        [
                            'kode' => 'TJKT',
                            'nama' => 'Teknik Jaringan Komputer dan Telekomunikasi',
                            'deskripsi' => 'Mempelajari perakitan dan pemeliharaan komputer, instalasi jaringan kabel dan nirkabel, administrasi server Linux/Windows, mikrotik routing, serta dasar keamanan siber.',
                            'prospek' => 'Network administrator, teknisi fiber optik/ISP, teknisi komputer kantor, staf IT helpdesk instansi.',
                        ],
                        [
                            'kode' => 'MPLB',
                            'nama' => 'Manajemen Perkantoran dan Layanan Bisnis',
                            'deskripsi' => 'Mempelajari tata kelola administrasi dokumen perkantoran, korespondensi digital, kearsipan elektronik, komunikasi bisnis, dan pelayanan prima kepada pelanggan.',
                            'prospek' => 'Staf administrasi perkantoran instansi pemerintah/swasta, resepsionis, staf data entry, sekretaris junior.',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($informasiData as $info) {
            InformasiSekolah::updateOrCreate(['key' => $info['key']], $info);
        }
    }
}
