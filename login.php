<?php
require_once 'config.php';
if (!empty($_SESSION['user'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([trim($_POST['username'] ?? '')]);
    $u = $stmt->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$u['id'], 'name'=>$u['name'], 'username'=>$u['username'], 'role'=>$u['role']];
        header('Location: index.php'); exit;
    }
    $error = 'Nama pengguna atau password tidak sesuai.';
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Masuk | KopiKita</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-body"><main class="login-card"><div class="brand-mark">☕</div><p class="eyebrow">CAFE SISTEM MANAJEMEN</p><h1>Selamat datang</h1><p class="muted">Masuk untuk mengelola operasional kafe.</p>
<?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Nama pengguna</label><input name="username" required autofocus placeholder="Masukkan nama pengguna"><label>Kata sandi</label><input type="password" name="password" required placeholder="Masukkan kata sandi"><button class="btn primary full">Masuk ke Dasbor</button></form>
<p class="login-hint">Akun contoh dibuat melalui <code>setup.php</code>.</p></main></body></html>
