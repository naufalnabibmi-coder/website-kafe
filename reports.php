<?php
require_once 'config.php';require_login();
$from=$_GET['from']??date('Y-m-01');$to=$_GET['to']??date('Y-m-d');
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$from))$from=date('Y-m-01');
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$to))$to=date('Y-m-d');
$q=$pdo->prepare("SELECT o.*,u.name cashier FROM orders o JOIN users u ON u.id=o.cashier_id WHERE DATE(o.created_at) BETWEEN ? AND ? ORDER BY o.created_at DESC");$q->execute([$from,$to]);$orders=$q->fetchAll();
$total=array_sum(array_column($orders,'subtotal'));$count=count($orders);
$page='reports';include 'partials/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">ANALISIS PENJUALAN</p><h1>Laporan Penjualan</h1><p class="muted">Rekap transaksi berdasarkan rentang tanggal.</p></div><button class="btn secondary" onclick="window.print()">Cetak Laporan</button></section>
<section class="panel"><form method="get" class="filter-form"><div><label>Dari tanggal</label><input type="date" name="from" value="<?=e($from)?>"></div><div><label>Sampai tanggal</label><input type="date" name="to" value="<?=e($to)?>"></div><button class="btn primary">Tampilkan</button></form></section>
<div class="stats-grid two"><div class="stat-card"><p>Total Penjualan</p><h2><?=rupiah($total)?></h2><small>Periode <?=date('d/m/Y',strtotime($from))?> – <?=date('d/m/Y',strtotime($to))?></small></div><div class="stat-card"><p>Jumlah Transaksi</p><h2><?=$count?></h2><small>Transaksi tercatat pada periode</small></div></div>
<section class="panel"><div class="panel-head"><h3>Rincian Transaksi</h3><span class="muted"><?=$count?> data</span></div><div class="table-wrap"><table><thead><tr><th>Nomor Nota</th><th>Tanggal</th><th>Pelanggan</th><th>Kasir</th><th>Total</th><th>Rincian</th></tr></thead><tbody>
<?php foreach($orders as $o):?><tr><td><b><?=e($o['invoice_no'])?></b></td><td><?=date('d/m/Y H:i',strtotime($o['created_at']))?></td><td><?=e($o['customer_name'])?></td><td><?=e($o['cashier'])?></td><td class="money"><?=rupiah($o['subtotal'])?></td><td><a class="text-link" href="receipt.php?id=<?=$o['id']?>">Lihat nota</a></td></tr><?php endforeach;?>
<?php if(!$orders):?><tr><td colspan="6" class="empty">Tidak ada transaksi pada periode ini.</td></tr><?php endif;?></tbody></table></div></section>
<?php include 'partials/footer.php';?>
