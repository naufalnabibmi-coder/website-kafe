<?php
require_once 'config.php'; require_role(['admin']);
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $action=$_POST['action']??'';
 if($action==='save'){
  $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $cat=(int)($_POST['category_id']??0);
  $cost=(float)($_POST['cost_price']??0); $sell=(float)($_POST['sell_price']??0); $stock=(int)($_POST['stock']??0);
  if($name===''||$cost<0||$sell<0||$stock<0){flash('Data menu tidak valid.','error');}
  elseif($id){$q=$pdo->prepare('UPDATE products SET category_id=?,name=?,cost_price=?,sell_price=?,stock=? WHERE id=?');$q->execute([$cat?:null,$name,$cost,$sell,$stock,$id]);flash('Menu berhasil diperbarui.');}
  else{$q=$pdo->prepare('INSERT INTO products(category_id,name,cost_price,sell_price,stock) VALUES(?,?,?,?,?)');$q->execute([$cat?:null,$name,$cost,$sell,$stock]);flash('Menu berhasil ditambahkan.');}
 } elseif($action==='delete'){ $q=$pdo->prepare('UPDATE products SET active=0 WHERE id=?');$q->execute([(int)$_POST['id']]);flash('Menu dinonaktifkan.');}
 header('Location: products.php');exit;
}
$cats=$pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$list=$pdo->query('SELECT p.*,c.name category FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.active=1 ORDER BY p.id DESC')->fetchAll();
$page='products';include 'partials/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">KATALOG PRODUK</p><h1>Pengelolaan Menu</h1><p class="muted">Kelola daftar makanan, minuman, harga, dan stok.</p></div></section>
<section class="panel"><h3>Tambah Menu Baru</h3><form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save"><div><label>Nama menu</label><input name="name" required placeholder="Contoh: Es Kopi Susu"></div><div><label>Kategori</label><select name="category_id"><option value="">Pilih kategori</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>"><?=e($c['name'])?></option><?php endforeach;?></select></div><div><label>Harga modal (Rp)</label><input type="number" name="cost_price" min="0" required></div><div><label>Harga jual (Rp)</label><input type="number" name="sell_price" min="0" required></div><div><label>Stok awal</label><input type="number" name="stock" min="0" required value="0"></div><div class="form-action"><button class="btn primary">＋ Simpan Menu</button></div></form></section>
<section class="panel"><div class="panel-head"><div><h3>Daftar Menu</h3><p class="muted"><?=count($list)?> menu aktif</p></div><input class="search-input" id="tableSearch" placeholder="Cari menu..."></div><div class="table-wrap"><table id="dataTable"><thead><tr><th>Nama</th><th>Kategori</th><th>Harga Modal</th><th>Harga Jual</th><th>Laba Kotor</th><th>Stok</th><th>Tindakan</th></tr></thead><tbody>
<?php foreach($list as $p):?><tr><td><b><?=e($p['name'])?></b></td><td><?=e($p['category']??'-')?></td><td><?=rupiah($p['cost_price'])?></td><td class="money"><?=rupiah($p['sell_price'])?></td><td><?=rupiah($p['sell_price']-$p['cost_price'])?></td><td><span class="stock-badge <?=$p['stock']<=5?'low':''?>"><?=$p['stock']?></span></td><td><form method="post" onsubmit="return confirm('Nonaktifkan menu ini?')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$p['id']?>"><button class="btn tiny danger">Nonaktifkan</button></form></td></tr><?php endforeach;?>
<?php if(!$list):?><tr><td colspan="7" class="empty">Belum ada menu.</td></tr><?php endif;?></tbody></table></div></section>
<?php include 'partials/footer.php';?>
