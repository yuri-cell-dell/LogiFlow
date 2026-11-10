<?php
require_once 'config/database.php'; require_once 'includes/functions.php';
$pageTitle='Produtos'; verify_csrf();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $action=$_POST['action']??'';
  if($action==='save'){
    $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $code=trim($_POST['code']??''); $desc=trim($_POST['description']??''); $unit=trim($_POST['unit']??'Un.');
    if($name===''||$code===''){flash('Informe o nome e o código do produto.','danger'); redirect('products.php');}
    try {
      if($id){$q=$pdo->prepare("UPDATE products SET name=?,code=?,description=?,unit=? WHERE id=?");$q->execute([$name,$code,$desc,$unit,$id]);}
      else {$q=$pdo->prepare("INSERT INTO products(name,code,description,unit) VALUES(?,?,?,?)");$q->execute([$name,$code,$desc,$unit]);}
      flash('Produto salvo com sucesso.');
    } catch(PDOException $e){flash('Não foi possível salvar. Verifique se o código já está cadastrado.','danger');}
    redirect('products.php');
  }
  if($action==='delete'){try{$q=$pdo->prepare("DELETE FROM products WHERE id=?");$q->execute([(int)$_POST['id']]);flash('Produto excluído.');}catch(PDOException $e){flash('Produto vinculado a um frete não pode ser excluído.','warning');}redirect('products.php');}
}
$edit=null;if(isset($_GET['edit'])){$q=$pdo->prepare("SELECT * FROM products WHERE id=?");$q->execute([(int)$_GET['edit']]);$edit=$q->fetch();}
$rows=$pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
require 'includes/header.php';
?>
<div class="page-heading"><div><h1>Produtos</h1><p>Cadastre e organize os produtos transportados.</p></div></div>
<div class="row g-4"><div class="col-lg-4"><div class="card"><div class="card-header"><?= $edit?'Editar produto':'Novo produto' ?></div><div class="card-body">
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<div class="mb-3"><label class="form-label">Nome do produto *</label><input class="form-control" name="name" required value="<?= e($edit['name']??'') ?>"></div>
<div class="mb-3"><label class="form-label">Código / SKU *</label><input class="form-control" name="code" required value="<?= e($edit['code']??'') ?>"></div>
<div class="mb-3"><label class="form-label">Descrição</label><textarea class="form-control" name="description" rows="2"><?= e($edit['description']??'') ?></textarea></div>
<div class="mb-3"><label class="form-label">Unidade</label><select class="form-select" name="unit"><?php foreach(['Un.','Kg','Sacas','Caixas','Litros','Toneladas'] as $u): ?><option <?= ($edit['unit']??'Un.')===$u?'selected':'' ?>><?= e($u) ?></option><?php endforeach; ?></select></div>
<button class="btn btn-primary">Salvar produto</button> <a class="btn btn-light" href="products.php">Limpar</a></form></div></div></div>
<div class="col-lg-8"><div class="card"><div class="card-header">Produtos cadastrados</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Código</th><th>Produto</th><th>Unidade</th><th>Ações</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="4" class="empty-state">Nenhum produto cadastrado.</td></tr><?php endif; foreach($rows as $r): ?><tr><td><?= e($r['code']) ?></td><td><?= e($r['name']) ?></td><td><?= e($r['unit']) ?></td><td><a class="btn btn-sm btn-light" href="?edit=<?= (int)$r['id'] ?>">Editar</a> <form class="d-inline" method="post" onsubmit="return confirm('Excluir este produto?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn-sm btn-outline-danger">Excluir</button></form></td></tr><?php endforeach; ?>
</tbody></table></div></div></div></div>
<?php require 'includes/footer.php'; ?>
