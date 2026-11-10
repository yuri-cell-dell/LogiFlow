<?php
require_once 'config/database.php';require_once 'includes/functions.php';
$pageTitle='Fretes';verify_csrf();
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='save'){
 $date=$_POST['freight_date']??'';$carrier=(int)($_POST['carrier_id']??0);$origin=(int)($_POST['origin_id']??0);$destination=(int)($_POST['destination_id']??0);
 $productIds=$_POST['product_id']??[];$quantities=$_POST['quantity']??[];
 if(!$date||!$carrier||!$origin||!$destination||$origin===$destination||!is_array($productIds)||count($productIds)===0){flash('Preencha os dados do frete, selecione origem e destino diferentes e adicione ao menos um produto.','danger');redirect('freights.php');}
 try{
  $pdo->beginTransaction();
  $q=$pdo->prepare("INSERT INTO freights(freight_date,carrier_id,origin_id,destination_id) VALUES(?,?,?,?)");$q->execute([$date,$carrier,$origin,$destination]);$freightId=(int)$pdo->lastInsertId();
  $item=$pdo->prepare("INSERT INTO freight_products(freight_id,product_id,quantity) VALUES(?,?,?)");
  foreach($productIds as $i=>$pid){$pid=(int)$pid;$qty=(float)str_replace(',','.',$quantities[$i]??0);if($pid>0&&$qty>0)$item->execute([$freightId,$pid,$qty]);}
  $pdo->commit();flash("Frete #{$freightId} gerado com sucesso.");
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('Não foi possível gerar o frete. Confira os cadastros e tente novamente.','danger');}
 redirect('freights.php');
}
$products=$pdo->query("SELECT id,name,code,unit FROM products ORDER BY name")->fetchAll();
$carriers=$pdo->query("SELECT id,name FROM carriers ORDER BY name")->fetchAll();
$addresses=$pdo->query("SELECT id,label,city,state FROM addresses ORDER BY label")->fetchAll();
$rows=$pdo->query("SELECT f.*,c.name carrier,o.label origin,d.label destination,(SELECT COUNT(*) FROM freight_products fp WHERE fp.freight_id=f.id) items FROM freights f JOIN carriers c ON c.id=f.carrier_id JOIN addresses o ON o.id=f.origin_id JOIN addresses d ON d.id=f.destination_id ORDER BY f.id DESC LIMIT 30")->fetchAll();
require 'includes/header.php';
?>
<div class="page-heading"><div><h1>Fretes</h1><p>Gere fretes relacionando transportador, endereços e produtos.</p></div></div>
<?php if(!$products||!$carriers||count($addresses)<2): ?><div class="alert alert-warning">Antes de gerar um frete, cadastre pelo menos um produto, um transportador e dois endereços.</div><?php endif; ?>
<div class="card mb-4"><div class="card-header">Novo frete</div><div class="card-body"><form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save">
<div class="row g-3 mb-3">
<div class="col-md-3"><label class="form-label">Data do frete *</label><input type="date" class="form-control" name="freight_date" required value="<?= date('Y-m-d') ?>"></div>
<div class="col-md-3"><label class="form-label">Transportador *</label><select class="form-select" name="carrier_id" required><option value="">Selecione</option><?php foreach($carriers as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Origem *</label><select class="form-select" name="origin_id" required><option value="">Selecione</option><?php foreach($addresses as $a): ?><option value="<?= (int)$a['id'] ?>"><?= e($a['label'].' — '.$a['city'].'/'.$a['state']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Destino *</label><select class="form-select" name="destination_id" required><option value="">Selecione</option><?php foreach($addresses as $a): ?><option value="<?= (int)$a['id'] ?>"><?= e($a['label'].' — '.$a['city'].'/'.$a['state']) ?></option><?php endforeach; ?></select></div>
</div>
<div class="d-flex justify-content-between align-items-center mb-2"><strong class="small">Produtos vinculados ao frete</strong><button type="button" class="btn btn-sm btn-outline-primary" data-add-product>+ Adicionar produto</button></div>
<div id="product-list">
<div class="product-row row g-2 align-items-end">
<div class="col-md-8"><label class="form-label">Produto</label><select class="form-select" name="product_id[]" required><option value="">Selecione um produto</option><?php foreach($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e($p['code'].' — '.$p['name'].' ('.$p['unit'].')') ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Quantidade</label><input type="number" min="0.01" step="0.01" class="form-control" name="quantity[]" required placeholder="Ex.: 10"></div>
<div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100" data-remove-product title="Remover">×</button></div>
</div>
</div>
<template id="product-template"><div class="product-row row g-2 align-items-end"><div class="col-md-8"><label class="form-label">Produto</label><select class="form-select" name="product_id[]" required><option value="">Selecione um produto</option><?php foreach($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e($p['code'].' — '.$p['name'].' ('.$p['unit'].')') ?></option><?php endforeach; ?></select></div><div class="col-md-3"><label class="form-label">Quantidade</label><input type="number" min="0.01" step="0.01" class="form-control" name="quantity[]" required placeholder="Ex.: 10"></div><div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100" data-remove-product>×</button></div></div></template>
<div class="mt-3"><button class="btn btn-primary" <?= (!$products||!$carriers||count($addresses)<2)?'disabled':'' ?>>Gerar frete</button></div>
</form></div></div>
<div class="card"><div class="card-header">Fretes cadastrados</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Nº</th><th>Data</th><th>Transportador</th><th>Origem → Destino</th><th>Produtos</th><th>Status</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="6" class="empty-state">Nenhum frete cadastrado.</td></tr><?php endif; foreach($rows as $r): ?><tr><td>#<?= (int)$r['id'] ?></td><td><?= date('d/m/Y',strtotime($r['freight_date'])) ?></td><td><?= e($r['carrier']) ?></td><td><?= e($r['origin'].' → '.$r['destination']) ?></td><td><?= (int)$r['items'] ?> item(ns)</td><td><span class="badge badge-soft"><?= e($r['status']) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
