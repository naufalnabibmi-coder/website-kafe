<?php
require_once 'config.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $items=json_decode($_POST['cart_json']??'[]',true); $payment=(float)($_POST['payment']??0); $customer=trim($_POST['customer_name']??'Umum');
 if(!is_array($items)||!count($items)){flash('Keranjang masih kosong.','error');header('Location: pos.php');exit;}
 try{
  $pdo->beginTransaction();$subtotal=0;$validated=[];
  foreach($items as $item){
   $pid=(int)($item['id']??0);$qty=(int)($item['qty']??0);
   if($qty<1) throw new Exception('Jumlah menu tidak valid.');
   $q=$pdo->prepare('SELECT * FROM products WHERE id=? AND active=1 FOR UPDATE');$q->execute([$pid]);$p=$q->fetch();
   if(!$p||$p['stock']<$qty) throw new Exception('Stok tidak cukup untuk '.($p['name']??'menu').'.');
   $line=(float)$p['sell_price']*$qty;$subtotal+=$line;$validated[]=[$p,$qty,$line];
  }
  if($payment<$subtotal) throw new Exception('Nominal pembayaran kurang dari total.');
  $invoice='INV'.date('ymdHis').random_int(10,99);
  $q=$pdo->prepare('INSERT INTO orders(invoice_no,cashier_id,customer_name,subtotal,payment,change_amount) VALUES(?,?,?,?,?,?)');
  $q->execute([$invoice,$_SESSION['user']['id'],$customer?:'Umum',$subtotal,$payment,$payment-$subtotal]);$orderId=(int)$pdo->lastInsertId();
  foreach($validated as [$p,$qty,$line]){
   $q=$pdo->prepare('INSERT INTO order_items(order_id,product_id,product_name,qty,price,line_total) VALUES(?,?,?,?,?,?)');
   $q->execute([$orderId,$p['id'],$p['name'],$qty,$p['sell_price'],$line]);
   $pdo->prepare('UPDATE products SET stock=stock-? WHERE id=?')->execute([$qty,$p['id']]);
   $pdo->prepare("INSERT INTO stock_movements(product_id,movement_type,qty,note,user_id) VALUES(?,'keluar',?,?,?)")->execute([$p['id'],$qty,'Penjualan '.$invoice,$_SESSION['user']['id']]);
  }
  $pdo->commit();header('Location: receipt.php?id='.$orderId);exit;
 }catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();flash($ex->getMessage(),'error');header('Location: pos.php');exit;}
}
$products=$pdo->query('SELECT id,name,sell_price,stock FROM products WHERE active=1 AND stock>0 ORDER BY name')->fetchAll();
$page='pos';include 'partials/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">KASIR</p><h1>Transaksi Baru</h1><p class="muted">Pilih menu dan masukkan pesanan pelanggan.</p></div></section>
<div class="pos-layout"><section class="panel"><div class="panel-head"><h3>Daftar Menu</h3><input id="productSearch" class="search-input" placeholder="Cari menu..."></div><div class="product-grid" id="productGrid">
<?php foreach($products as $p):?><button type="button" class="product-tile" data-id="<?=$p['id']?>" data-name="<?=e($p['name'])?>" data-price="<?=$p['sell_price']?>" data-stock="<?=$p['stock']?>"><span class="product-emoji">☕</span><b><?=e($p['name'])?></b><strong><?=rupiah($p['sell_price'])?></strong><small>Stok: <?=$p['stock']?></small></button><?php endforeach;?>
<?php if(!$products):?><p class="empty">Tidak ada menu dengan stok tersedia.</p><?php endif;?></div></section>
<section class="panel cart-panel"><h3>Pesanan</h3><label>Nama pelanggan</label><input id="customerName" placeholder="Umum"><div id="cartItems" class="cart-items"><p class="empty">Pilih menu untuk memulai pesanan.</p></div><div class="cart-total"><span>Total</span><strong id="cartTotal">Rp0</strong></div><form method="post" id="checkoutForm"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="cart_json" id="cartJson"><input type="hidden" name="customer_name" id="customerField"><label>Uang diterima (Rp)</label><input type="number" name="payment" id="payment" min="0" required placeholder="Masukkan jumlah pembayaran"><button class="btn primary full" type="submit">Proses Pembayaran</button></form></section></div>
<?php include 'partials/footer.php';?>
