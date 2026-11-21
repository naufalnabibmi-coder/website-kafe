<?php
require_once 'config.php';

$defaults = [
    ['Administrator', 'admin', 'admin123', 'admin'],
    ['Kasir Kafe', 'kasir', 'kasir123', 'kasir'],
];
foreach ($defaults as [$name, $username, $password, $role]) {
    $check = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $check->execute([$username]);
    if (!$check->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO users(name,username,password,role) VALUES(?,?,?,?)');
        $stmt->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT), $role]);
    }
}
echo '<h2>Setup akun selesai</h2><p>Admin: <b>admin</b> / <b>admin123</b></p><p>Kasir: <b>kasir</b> / <b>kasir123</b></p><p>Hapus file setup.php setelah berhasil membuat akun.</p><p><a href="login.php">Buka halaman login</a></p>';
