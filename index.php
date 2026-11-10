<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
$pageTitle = 'Visão geral';
$counts = [
 'products' => (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
 'addresses' => (int)$pdo->query("SELECT COUNT(*) FROM addresses")->fetchColumn(),
 'carriers' => (int)$pdo->query("SELECT COUNT(*) FROM carriers")->fetchColumn(),
 'freights' => (int)$pdo->query("SELECT COUNT(*) FROM freights WHERE freight_date = CURDATE()")->fetchColumn()
];
$recent = $pdo->query("SELECT f.id, f.freight_date, f.status, c.name carrier FROM freights f JOIN carriers c ON c.id=f.carrier_id ORDER BY f.id DESC LIMIT 5")->fetchAll();
require 'includes/header.php';
?>
<div class="page-heading"><div><h1>Visão geral</h1><p>Acompanhe os principais indicadores da operação logística.</p></div><a class="btn btn-primary" href="freights.php">+ Novo frete</a></div>
<div class="row g-3 mb-4">
 <?php foreach ([['Fretes hoje',$counts['freights'],'⇄'],['Produtos cadastrados',$counts['products'],'▣'],['Endereços cadastrados',$counts['addresses'],'⌖'],['Transportadores',$counts['carriers'],'▤']] as $s): ?>
 <div class="col-6 col-xl-3"><div class="card stat-card"><div><div class="label"><?= e($s[0]) ?></div><div class="value"><?= $s[1] ?></div></div><div class="stat-icon"><?= e($s[2]) ?></div></div></div>
 <?php endforeach; ?>
</div>
<div class="card"><div class="card-header d-flex justify-content-between"><span>Fretes recentes</span><a class="small text-decoration-none" href="freights.php">Ver todos</a></div><div class="table-responsive">
<table class="table mb-0"><thead><tr><th>Nº</th><th>Data</th><th>Transportador</th><th>Situação</th></tr></thead><tbody>
<?php if (!$recent): ?><tr><td colspan="4" class="empty-state">Nenhum frete cadastrado. Comece criando um novo frete.</td></tr>
<?php else: foreach($recent as $r): ?><tr><td>#<?= (int)$r['id'] ?></td><td><?= date('d/m/Y',strtotime($r['freight_date'])) ?></td><td><?= e($r['carrier']) ?></td><td><span class="badge badge-soft"><?= e($r['status']) ?></span></td></tr><?php endforeach; endif; ?>
</tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
