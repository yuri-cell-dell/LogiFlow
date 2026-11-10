<?php
require_once 'config/database.php'; require_once 'includes/functions.php';
$pageTitle='Transportadores';verify_csrf();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $action=$_POST['action']??'';
 if($action==='save'){
  $id=(int)($_POST['id']??0);$v=[trim($_POST['name']??''),trim($_POST['document']??''),trim($_POST['phone']??''),trim($_POST['email']??''),trim($_POST['vehicle_type']??'')];
  if($v[0]===''){flash('Informe o nome do transportador.','danger');redirect('carriers.php');}
  if($id){$q=$pdo->prepare("UPDATE carriers SET name=?,document=?,phone=?,email=?,vehicle_type=? WHERE id=?");$q->execute([...$v,$id]);}
  else{$q=$pdo->prepare("INSERT INTO carriers(name,document,phone,email,vehicle_type) VALUES(?,?,?,?,?)");$q->execute($v);}
  flash('Transportador salvo com sucesso.');redirect('carriers.php');
 }
 if($action==='delete'){try{$q=$pdo->prepare("DELETE FROM carriers WHERE id=?");$q->execute([(int)$_POST['id']]);flash('Transportador excluído.');}catch(PDOException $e){flash('Transportador vinculado a um frete não pode ser excluído.','warning');}redirect('carriers.php');}
}
$edit=null;if(isset($_GET['edit'])){$q=$pdo->prepare("SELECT * FROM carriers WHERE id=?");$q->execute([(int)$_GET['edit']]);$edit=$q->fetch();}
$rows=$pdo->query("SELECT * FROM carriers ORDER BY id DESC")->fetchAll();require 'includes/header.php';
?>
<div class="page-heading"><div><h1>Transportadores</h1><p>Gerencie os responsáveis pelo transporte.</p></div></div>
<div class="row g-4"><div class="col-lg-4"><div class="card"><div class="card-header"><?= $edit?'Editar transportador':'Novo transportador' ?></div><div class="card-body"><form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<div class="mb-3"><label class="form-label">Nome / Razão social *</label><input class="form-control" name="name" required value="<?= e($edit['name']??'') ?>"></div>
<div class="mb-3"><label class="form-label">CPF / CNPJ</label><input class="form-control" name="document" value="<?= e($edit['document']??'') ?>"></div>
<div class="mb-3"><label class="form-label">Telefone</label><input class="form-control" name="phone" value="<?= e($edit['phone']??'') ?>"></div>
<div class="mb-3"><label class="form-label">E-mail</label><input type="email" class="form-control" name="email" value="<?= e($edit['email']??'') ?>"></div>
<div class="mb-3"><label class="form-label">Tipo de veículo</label><input class="form-control" name="vehicle_type" placeholder="Ex.: Caminhão" value="<?= e($edit['vehicle_type']??'') ?>"></div>
<button class="btn btn-primary">Salvar transportador</button> <a class="btn btn-light" href="carriers.php">Limpar</a></form></div></div></div>
<div class="col-lg-8"><div class="card"><div class="card-header">Transportadores cadastrados</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Nome</th><th>Veículo</th><th>Contato</th><th>Ações</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="4" class="empty-state">Nenhum transportador cadastrado.</td></tr><?php endif; foreach($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['vehicle_type']) ?></td><td><?= e($r['phone']) ?></td><td><a class="btn btn-sm btn-light" href="?edit=<?= (int)$r['id'] ?>">Editar</a> <form class="d-inline" method="post" onsubmit="return confirm('Excluir este transportador?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn-sm btn-outline-danger">Excluir</button></form></td></tr><?php endforeach; ?>
</tbody></table></div></div></div></div>
<?php require 'includes/footer.php'; ?>
