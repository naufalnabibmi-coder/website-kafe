<?php
require_once 'config.php'; require_login();
$today = $pdo->query("SELECT COALESCE(SUM(subtotal),0) revenue, COUNT(*) total FROM orders WHERE DATE(created_at)=CURDATE()")->fetch();
$month = $pdo->query("SELECT COALESCE(SUM(subtotal),0) revenue FROM orders WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())")->fetchColumn();
$products = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE active=1")->fetchColumn();
$low = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE active=1 AND stock<=5")->fetchColumn();
$recent = $pdo->query("SELECT o.*,u.name cashier FROM orders o JOIN users u ON u.id=o.cashier_id ORDER BY o.id DESC LIMIT 6")->fetchAll();
$daily = $pdo->query("SELECT DATE(created_at) day, SUM(subtotal) total FROM orders WHERE created_at >= CURDATE()-INTERVAL 6 DAY GROUP BY DATE(created_at) ORDER BY day")->fetchAll();
$labels=[]; $values=[];
for($i=6;$i>=0;$i--){$d=date('Y-m-d',strtotime("-$i day"));$labels[]=date('d/m',strtotime($d));$values[$d]=0;}
foreach($daily as $d){$values[$d['day']]=(float)$d['total'];}
$chartValues=array_values($values);
$page='dasbor'; include 'partials/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">RINGKASAN OPERASIONAL</p><h1>Dasbor</h1><p class="muted">Pantau performa kafe hari ini.</p></div><span class="date-chip"><?=date('d F Y')?></span></section>
<div class="stats-grid">
<div class="stat-card"><span class="stat-icon purple">↗</span><p>Penjualan Hari Ini</p><h2><?=rupiah($today['revenue'])?></h2><small><?=number_format((int)$today['total'])?> transaksi</small></div>
<div class="stat-card"><span class="stat-icon green">▣</span><p>Penjualan Bulan Ini</p><h2><?=rupiah($month)?></h2><small>Total bulan berjalan</small></div>
<div class="stat-card"><span class="stat-icon orange">☕</span><p>Menu Aktif</p><h2><?=$products?></h2><small>Menu tersedia di katalog</small></div>
<div class="stat-card"><span class="stat-icon red">!</span><p>Stok Menipis</p><h2><?=$low?></h2><small>Menu dengan stok ≤ 5</small></div>
</div>
<div class="content-grid"><section class="panel"><div class="panel-head"><div><h3>Penjualan 7 Hari Terakhir</h3><p class="muted">Ringkasan pendapatan per hari</p></div></div><canvas id="salesChart" height="115"></canvas></section>
<section class="panel quick-panel"><h3>Akses Cepat</h3><a class="quick-link" href="pos.php"><span>＋</span><div><b>Buat Transaksi</b><small>Catat pesanan pelanggan</small></div></a><a class="quick-link" href="products.php"><span>☷</span><div><b>Kelola Menu</b><small>Atur harga dan stok</small></div></a><a class="quick-link" href="reports.php"><span>▤</span><div><b>Lihat Laporan</b><small>Rekap penjualan kafe</small></div></a></section></div>
<section class="panel"><div class="panel-head"><div><h3>Transaksi Terbaru</h3><p class="muted">Aktivitas penjualan terbaru</p></div><a href="reports.php" class="text-link">Lihat semua →</a></div><div class="table-wrap"><table><thead><tr><th>Nomor Nota</th><th>Waktu</th><th>Kasir</th><th>Total</th></tr></thead><tbody>
<?php foreach($recent as $o): ?><tr><td><b><?=e($o['invoice_no'])?></b></td><td><?=date('d/m/Y H:i',strtotime($o['created_at']))?></td><td><?=e($o['cashier'])?></td><td class="money"><?=rupiah($o['subtotal'])?></td></tr><?php endforeach; ?>
<?php if(!$recent): ?><tr><td colspan="4" class="empty">Belum ada transaksi.</td></tr><?php endif; ?></tbody></table></div></section>
<script>window.salesLabels=<?=json_encode($labels)?>;window.salesValues=<?=json_encode($chartValues)?>;</script>
<?php include 'partials/footer.php'; ?>
