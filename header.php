<?php
$user=$_SESSION['user']; $page=$page??'';
$flashMessage=$_SESSION['flash']??null; unset($_SESSION['flash']);
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e((['dashboard'=>'Dasbor','pos'=>'Kasir','products'=>'Menu','stock'=>'Stok','reports'=>'Laporan'][$page]??ucfirst($page)))?> | KopiKita</title><link rel="stylesheet" href="assets/style.css"><script src="https://cdn.jsdelivr.net/npm/chart.js"></script></head><body>
<div class="app-shell"><aside class="sidebar" id="sidebar"><a class="brand" href="index.php"><span class="brand-mark small">☕</span><span>Kopi<span class="brand-accent">Kita</span><small>SISTEM MANAJEMEN</small></span></a><div class="nav-label">MENU UTAMA</div>
<a class="nav-item <?=$page==='dasbor'?'active':''?>" href="index.php"><span>▦</span>Dasbor</a>
<a class="nav-item <?=$page==='pos'?'active':''?>" href="pos.php"><span>＋</span>Transaksi Kasir</a>
<?php if($user['role']==='admin'): ?><a class="nav-item <?=$page==='products'?'active':''?>" href="products.php"><span>☷</span>Pengelolaan Menu</a><a class="nav-item <?=$page==='stock'?'active':''?>" href="stock.php"><span>▤</span>Pengelolaan Stok</a><?php endif; ?>
<a class="nav-item <?=$page==='reports'?'active':''?>" href="reports.php"><span>▧</span>Laporan Penjualan</a>
<div class="sidebar-bottom"><div class="user-mini"><div class="avatar"><?=e(strtoupper(substr($user['name'],0,1)))?></div><div><b><?=e($user['name'])?></b><small><?=e(ucfirst($user['role']))?></small></div></div><a class="nav-item logout" href="logout.php"><span>↪</span>Keluar</a></div></aside>
<main class="main-area"><header class="topbar"><button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button><div class="topbar-title">Ruang kerja kafe <span>/ <?=e((['dashboard'=>'Dasbor','pos'=>'Kasir','products'=>'Menu','stock'=>'Stok','reports'=>'Laporan'][$page]??ucfirst($page)))?></span></div><div class="topbar-user"><span class="online-dot"></span><?=e($user['name'])?></div></header><div class="page-content">
<?php if($flashMessage): ?><div class="alert <?=e($flashMessage['type'])?>"><?=e($flashMessage['message'])?></div><?php endif; ?>
