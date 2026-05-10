<?php
/*
  FUNGSI: Router multi-halaman + data biodata + simpan ulasan.
  CSS    => percobaan_2.css
  ENTRY  => percobaan_1.html (redirect kesini)
  DATA   => ulasan.json (dibuat otomatis)
*/

// HANDLE FORM ULASAN (POST)
// Vercel memiliki read-only file system, jadi kita gunakan folder /tmp jika berjalan di Vercel
$ulasan_file = getenv('VERCEL') ? '/tmp/ulasan.json' : __DIR__ . '/../ulasan.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ulasan'])) {
    $nama_rv = htmlspecialchars(trim($_POST['nama_reviewer'] ?? ''), ENT_QUOTES);
    $pesan   = htmlspecialchars(trim($_POST['pesan_ulasan']  ?? ''), ENT_QUOTES);
    $rating  = max(1, min(5, intval($_POST['rating'] ?? 5)));

    if (!empty($pesan)) {
        // Baca data lama
        $list = [];
        if (file_exists($ulasan_file)) {
            $list = json_decode(file_get_contents($ulasan_file), true) ?? [];
        }
        // Tambahkan ulasan baru di depan
        array_unshift($list, [
            'nama'   => !empty($nama_rv) ? $nama_rv : 'Anonim',
            'pesan'  => $pesan,
            'rating' => $rating,
            'waktu'  => date('d M Y, H:i'),
        ]);
        @file_put_contents($ulasan_file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    header("Location: index.php?page=ulasan");
    exit();
}

// DATA BIODATA
$nama      = "Abdullah Azwar Anas";
$umur      = 18;
$kelas     = "A";
$prodi     = "Sistem Teknologi Informasi";
$sekolah   = "Universitas Muhammadiyah Pringsewu";
$alamat    = "Jl. Jendral Sudirman No. 123";
$email     = "abdullahazwaranas@gmail.com";
$telepon   = "+62 000 0000 0000";
$hobi      = "Membaca, dan Bermain Game";
$cita_cita = "Programmer";
$motivasi  = "Kualitas menentukan kredibilitas masa depan";
$quote     = "Dedikasi pada detail adalah fondasi utama menuju keunggulan profesional sejati.";
$deskripsi = "Halo! Saya mahasiswa Sistem Teknologi Informasi yang antusias mempelajari dunia pemrograman dan pengembangan web. Saya fokus mengasah keterampilan teknis untuk menciptakan solusi digital inovatif. Mari terhubung dan berkolaborasi bersama saya.";
$foto      = "foto.png";           // Nama file foto di folder smester_2

// SETUP
$page        = $_GET['page'] ?? 'beranda';
$valid_pages = ['beranda', 'profil', 'tentang', 'ulasan'];
if (!in_array($page, $valid_pages)) $page = 'beranda';

$inisial = strtoupper(substr(trim($nama), 0, 1));
$tahun   = date("Y");
$sukses  = (isset($_GET['sukses']) && $_GET['sukses'] === '1');

// Load ulasan
$ulasan_list = [];
if (file_exists($ulasan_file)) {
    $ulasan_list = json_decode(file_get_contents($ulasan_file), true) ?? [];
}

// Helpers
/** Cetak bintang dari angka rating */
function bintang(int $n): string {
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}

/** Tampilkan nilai atau placeholder abu-abu */
function tampil(string $v, string $ph = 'Belum diisi'): string {
    $v = trim($v);
    if ($v === '' || $v === '0') {
        return '<span class="info-value empty">' . htmlspecialchars($ph) . '</span>';
    }
    return '<span class="info-value">' . htmlspecialchars($v) . '</span>';
}

/** Nama dipenggal untuk logo navbar (2 kata) */
$words     = explode(' ', $nama);
$logo_word = array_slice($words, 0, 2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Biodata <?= $nama ?> – <?= $prodi ?>, <?= $sekolah ?>">
    <title><?= ucfirst($page) ?> — <?= $nama ?></title>
    <link rel="stylesheet" href="../percobaan_2.css?v=<?= time() ?>">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">

    <a href="?page=beranda" class="nav-logo">
        <?php foreach ($logo_word as $i => $w): ?>
            <span class="w<?= ($i % 3) + 1 ?>"><?= htmlspecialchars($w) ?></span>
        <?php endforeach; ?>
    </a>

    <button class="nav-toggle" id="navToggle" aria-label="Buka menu">
        <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" id="navLinks">
        <?php
        $menu = ['beranda' => 'Beranda', 'profil' => 'Profil', 'tentang' => 'Tentang', 'ulasan' => 'Ulasan'];
        foreach ($menu as $k => $label):
            $cls = ($page === $k) ? 'active' : '';
        ?>
        <li><a href="?page=<?= $k ?>" class="<?= $cls ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
    </ul>

</nav>

<!-- PAGE: BERANDA -->
<?php if ($page === 'beranda'): ?>
<main class="page active">
    <section class="hero">

        <!-- Kiri: teks -->
        <div class="hero-left">
            <p class="hero-tagline">
                WELCOME
            </p>

            <h1 class="hero-heading">
                Halo, Saya<br>
                <span class="hi"><?= htmlspecialchars($nama) ?>!</span>
            </h1>

            <p class="hero-desc">
                <?php if (!empty(trim($deskripsi))): ?>
                    <?= htmlspecialchars($deskripsi) ?>
                <?php else: ?>
                    Mahasiswa <strong><?= htmlspecialchars($prodi) ?></strong>
                    di <strong><?= htmlspecialchars($sekolah) ?></strong>.
                    <?= htmlspecialchars($motivasi) ?> 🚀
                <?php endif; ?>
            </p>

                <a href="?page=profil" class="btn-primary">
                    Lihat Profil <span class="arrow">→</span>
                </a>

                <a href="?page=ulasan" class="btn-ulasan">
                    Beri Ulasan <span class="arrow">→</span>
                </a>

            <!-- Quick stats -->
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-value"><?= $umur ?></span>
                    <span class="stat-label">Tahun</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= htmlspecialchars($kelas) ?></span>
                    <span class="stat-label">Kelas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= count($ulasan_list) ?></span>
                    <span class="stat-label">Ulasan</span>
                </div>
            </div>
        </div>

        <!-- Kanan: visual -->
        <div class="hero-right">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>
            <div class="blob blob-4"></div>
            <div class="hero-avatar-wrap">
                <div class="hero-ring">
                    <?php if (!empty($foto) && file_exists(__DIR__ . '/../' . $foto)): ?>
                        <img src="../<?= htmlspecialchars($foto) ?>" alt="Foto <?= htmlspecialchars($nama) ?>" class="hero-img">
                    <?php else: ?>
                        <span class="hero-initial"><?= $inisial ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </section>
</main>


<!-- PAGE: PROFIL -->
<?php elseif ($page === 'profil'): ?>
<main class="page active">
    <div class="inner">

        <!-- Banner atas -->
        <div class="profile-banner">
            <div class="banner-avatar">
                <?php if (!empty($foto) && file_exists(__DIR__ . '/../' . $foto)): ?>
                    <img src="../<?= htmlspecialchars($foto) ?>" alt="Foto" class="banner-img">
                <?php else: ?>
                    <?= $inisial ?>
                <?php endif; ?>
            </div>
            <div class="banner-info">
                <h2><?= htmlspecialchars($nama) ?></h2>
                <p style="margin-top:6px;opacity:.75;font-size:.83rem;">
                    <?= !empty(trim($email)) ? htmlspecialchars($email) : 'Email ' ?>
                </p>
            </div>
        </div>

        <div class="card-stack">

            <!-- Data Diri -->
            <div class="card">
                <h2 class="card-title">👤 Data Diri</h2>
                <div class="info-grid">

                    <div class="info-item">
                        <span class="info-label">Nama Lengkap</span>
                        <?= tampil($nama) ?>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Umur</span>
                        <span class="info-value"><?= $umur ?> Tahun</span>
                    </div>
                    <div class="info-item span2">
                        <span class="info-label">Program Studi</span>
                        <?= tampil($prodi) ?>
                    </div>
                    <div class="info-item span2">
                        <span class="info-label">Pendidikan Terakhir</span>
                        <?= tampil($sekolah) ?>
                    </div>
                    <div class="info-item span2">
                        <span class="info-label">Alamat</span>
                        <?= tampil($alamat) ?>
                    </div>

                </div>
            </div>

            <!-- Kontak -->
            <div class="card">
                <h2 class="card-title">📞 Kontak</h2>
                <div class="info-grid">
                    <div class="info-item span2">
                        <span class="info-label">Email</span>
                        <?= tampil($email, 'abdullahazwaranas@gmail.com') ?>
                    </div>
                    <div class="info-item span2">
                        <span class="info-label">Nomor Telepon</span>
                        <?= tampil($telepon, '08123456789') ?>
                    </div>
                </div>
            </div>

            <!-- Tentang Saya -->
            <div class="card">
                <h2 class="card-title">✨ Tentang Saya</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Hobi</span>
                        <?= tampil($hobi) ?>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cita-cita</span>
                        <?= tampil($cita_cita) ?>
                    </div>
                    <div class="info-item span2">
                        <span class="info-label">Motivasi Hidup</span>
                        <?= tampil($motivasi) ?>
                    </div>
                </div>
            </div>

        </div><!-- end card-stack -->
    </div>
</main>


<!-- PAGE: TENTANG -->
<?php elseif ($page === 'tentang'): ?>
<main class="page active">
    <div class="inner">

        <div class="page-head">
            <h1>Tentang Saya</h1>
            <p class="sub">Kenali lebih jauh siapa <?= htmlspecialchars($nama) ?></p>
            <div class="accent-bar"></div>
        </div>

        <!-- Quote besar -->
        <div class="quote-hero">
            <span class="qmark">"</span>
            <p class="quote-text"><?= htmlspecialchars($quote) ?></p>
            <span class="quote-by">— <?= htmlspecialchars($nama) ?></span>
        </div>

        <!-- Blok-blok info -->
        <div class="story-grid">

            <div class="story-block">
                <span class="story-icon">🎯</span>
                <p class="story-label">Cita-cita</p>
                <?php if (!empty(trim($cita_cita))): ?>
                    <p class="story-val"><?= htmlspecialchars($cita_cita) ?></p>
                <?php else: ?>
                    <p class="story-val empty">Belum diisi</p>
                <?php endif; ?>
            </div>

            <div class="story-block">
                <span class="story-icon">💡</span>
                <p class="story-label">Motivasi</p>
                <?php if (!empty(trim($motivasi))): ?>
                    <p class="story-val"><?= htmlspecialchars($motivasi) ?></p>
                <?php else: ?>
                    <p class="story-val empty">anak pendiam</p>
                <?php endif; ?>
            </div>

            <div class="story-block">
                <span class="story-icon">🎮</span>
                <p class="story-label">Hobi</p>
                <?php if (!empty(trim($hobi))): ?>
                    <p class="story-val"><?= htmlspecialchars($hobi) ?></p>
                <?php else: ?>
                    <p class="story-val empty">Belum diisi</p>
                <?php endif; ?>
            </div>

            <div class="story-block">
                <span class="story-icon">🏫</span>
                <p class="story-label">Kampus</p>
                <?php if (!empty(trim($sekolah))): ?>
                    <p class="story-val"><?= htmlspecialchars($sekolah) ?></p>
                <?php else: ?>
                    <p class="story-val empty">Belum diisi</p>
                <?php endif; ?>
            </div>

            <div class="story-block">
                <span class="story-icon">📍</span>
                <p class="story-label">Alamat</p>
                <?php if (!empty(trim($alamat))): ?>
                    <p class="story-val"><?= htmlspecialchars($alamat) ?></p>
                <?php else: ?>
                    <p class="story-val empty">Belum diisi</p>
                <?php endif; ?>
            </div>

            <div class="story-block">
                <span class="story-icon">📅</span>
                <p class="story-label">Umur</p>
                <p class="story-val"><?= $umur ?> Tahun</p>
            </div>

        </div>
    </div>
</main>


<!-- PAGE: ULASAN -->
<?php elseif ($page === 'ulasan'): ?>
<main class="page active">
    <div class="inner">

        <div class="page-head">
            <h1>Ulasan</h1>
            <p class="sub">Bagikan pendapatmu untuk <?= htmlspecialchars($nama) ?></p>
            <div class="accent-bar"></div>
        </div>

        <div class="ulasan-grid">

            <!-- FORM KIRIM ULASAN -->
            <aside class="form-card">
                <p class="form-card-title">Ulasan untuk saya💬</p>
                <p class="form-sub"><br></p>

                <?php if ($sukses): ?>
                <div class="alert-ok">✅ Ulasan berhasil dikirim, terima kasih!</div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=ulasan" novalidate>

                    <div class="fg">
                        <label for="nama_reviewer">Nama</label>
                        <input
                            type="text"
                            id="nama_reviewer"
                            name="nama_reviewer"
                            placeholder="Tulis namamu (opsional)..."
                            maxlength="80"
                            autocomplete="off"
                        >
                    </div>

                    <div class="fg">
                        <label>Rating</label>
                        <div class="stars">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input
                                    type="radio"
                                    id="star<?= $i ?>"
                                    name="rating"
                                    value="<?= $i ?>"
                                    <?= $i === 5 ? 'checked' : '' ?>
                                >
                                <label for="star<?= $i ?>" title="<?= $i ?> bintang">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="fg">
                        <label for="pesan_ulasan">Pesan / Ulasan <span style="color:#ef4444">*</span></label>
                        <textarea
                            id="pesan_ulasan"
                            name="pesan_ulasan"
                            placeholder="Tulis kesan, pesan, atau ulasanmu di sini..."
                            required
                        ></textarea>
                    </div>

                    <button type="submit" name="submit_ulasan" class="btn-send">
                        Kirim Ulasan ✉️
                    </button>

                </form>
            </aside>

            <!-- DAFTAR ULASAN -->
            <section class="reviews-col">

                <?php if (empty($ulasan_list)): ?>
                <div class="no-reviews">
                    <span class="nr-icon">💬</span>
                    <p>Belum ada ulasan.<br>Jadilah yang pertama!</p>
                </div>
                <?php else: ?>

                    <?php foreach ($ulasan_list as $u): ?>
                    <div class="review-card">
                        <div class="rv-head">
                            <div class="rv-user">
                                <div class="rv-ava">
                                    <?= strtoupper(substr($u['nama'], 0, 1)) ?>
                                </div>
                                <div>
                                    <span class="rv-name"><?= htmlspecialchars($u['nama']) ?></span>
                                    <span class="rv-time"><?= htmlspecialchars($u['waktu']) ?></span>
                                </div>
                            </div>
                            <div class="rv-stars"><?= bintang(intval($u['rating'])) ?></div>
                        </div>
                        <p class="rv-text">"<?= htmlspecialchars($u['pesan']) ?>"</p>
                    </div>
                    <?php endforeach; ?>

                <?php endif; ?>
            </section>

        </div><!-- end ulasan-grid -->
    </div>
</main>
<?php endif; ?>

<!-- JAVASCRIPT -->
<script>
/* ── Navbar shadow saat scroll ── */
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 8);
}, { passive: true });

/* ── Mobile hamburger toggle ── */
const toggle   = document.getElementById('navToggle');
const navLinks = document.getElementById('navLinks');

toggle.addEventListener('click', () => {
    const isOpen = navLinks.classList.toggle('open');
    toggle.classList.toggle('open', isOpen);
    toggle.setAttribute('aria-expanded', isOpen);
});

/* ── Tutup menu saat link diklik ── */
navLinks.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
        navLinks.classList.remove('open');
        toggle.classList.remove('open');
    });
});

/* ── Tutup menu saat klik di luar ── */
document.addEventListener('click', e => {
    if (!navbar.contains(e.target)) {
        navLinks.classList.remove('open');
        toggle.classList.remove('open');
    }
});
</script>

</body>
</html>
