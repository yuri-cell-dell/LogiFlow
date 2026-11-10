<?php
require_once 'config/database.php'; require_once 'includes/functions.php';
$pageTitle='Endereços'; verify_csrf();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $action=$_POST['action']??'';
 if($action==='save'){
  $id=(int)($_POST['id']??0);$v=[trim($_POST['label']??''),trim($_POST['zip_code']??''),trim($_POST['street']??''),trim($_POST['number']??''),trim($_POST['complement']??''),trim($_POST['city']??''),strtoupper(trim($_POST['state']??''))];
  if($v[0]===''||$v[2]===''||$v[5]===''||strlen($v[6])!==2){flash('Preencha identificação, logradouro, cidade e UF.','danger');redirect('addresses.php');}
  if($id){$q=$pdo->prepare("UPDATE addresses SET label=?,zip_code=?,street=?,number=?,complement=?,city=?,state=? WHERE id=?");$q->execute([...$v,$id]);}
  else{$q=$pdo->prepare("INSERT INTO addresses(label,zip_code,street,number,complement,city,state) VALUES(?,?,?,?,?,?,?)");$q->execute($v);}
  flash('Endereço salvo com sucesso.');redirect('addresses.php');
 }
 if($action==='delete'){try{$q=$pdo->prepare("DELETE FROM addresses WHERE id=?");$q->execute([(int)$_POST['id']]);flash('Endereço excluído.');}catch(PDOException $e){flash('Endereço vinculado a um frete não pode ser excluído.','warning');}redirect('addresses.php');}
}
$edit=null;if(isset($_GET['edit'])){$q=$pdo->prepare("SELECT * FROM addresses WHERE id=?");$q->execute([(int)$_GET['edit']]);$edit=$q->fetch();}
$rows=$pdo->query("SELECT * FROM addresses ORDER BY id DESC")->fetchAll();require 'includes/header.php';
?>
<div class="page-heading"><div><h1>Endereços</h1><p>Cadastre locais de coleta e entrega.</p></div></div>
<div class="row g-4"><div class="col-lg-4"><div class="card"><div class="card-header"><?= $edit?'Editar endereço':'Novo endereço' ?></div><div class="card-body"><form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<div class="mb-3"><label class="form-label">Identificação *</label><input class="form-control" name="label" required value="<?= e($edit['label']??'') ?>" placeholder="Ex.: Depósito Central"></div>
<div class="row"><div class="col-6 mb-3"><label class="form-label">CEP</label><input class="form-control" name="zip_code" value="<?= e($edit['zip_code']??'') ?>"></div><div class="col-6 mb-3"><label class="form-label">Número</label><input class="form-control" name="number" value="<?= e($edit['number']??'') ?>"></div></div>
<div class="mb-3"><label class="form-label">Rua / Avenida *</label><input class="form-control" name="street" required value="<?= e($edit['street']??'') ?>"></div>
<div class="mb-3"><label class="form-label">Complemento</label><input class="form-control" name="complement" value="<?= e($edit['complement']??'') ?>"></div>
<div class="row"><div class="col-8 mb-3"><label class="form-label">Cidade *</label><input class="form-control" name="city" required value="<?= e($edit['city']??'') ?>"></div><div class="col-4 mb-3"><label class="form-label">UF *</label><input class="form-control text-uppercase" name="state" maxlength="2" required value="<?= e($edit['state']??'PR') ?>"></div></div>
<button class="btn btn-primary">Salvar endereço</button> <a class="btn btn-light" href="addresses.php">Limpar</a></form></div></div></div>
<div class="col-lg-8"><div class="card"><div class="card-header">Endereços cadastrados</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Identificação</th><th>Localidade</th><th>Ações</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="3" class="empty-state">Nenhum endereço cadastrado.</td></tr><?php endif; foreach($rows as $r): ?><tr><td><?= e($r['label']) ?></td><td><?= e($r['city'].'/'.$r['state']) ?></td><td><a class="btn btn-sm btn-light" href="?edit=<?= (int)$r['id'] ?>">Editar</a> <form class="d-inline" method="post" onsubmit="return confirm('Excluir este endereço?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn-sm btn-outline-danger">Excluir</button></form></td></tr><?php endforeach; ?>
</tbody></table></div></div></div></div>
<?php require 'includes/footer.php'; ?>
