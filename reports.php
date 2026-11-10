<?php
require_once 'config/database.php';require_once 'includes/functions.php';
$pageTitle='Relatórios';
$date=$_GET['date']??date('Y-m-d');
$q=$pdo->prepare("SELECT f.id,f.freight_date,f.status,c.name carrier,o.label origin,d.label destination, GROUP_CONCAT(CONCAT(p.name,' (',fp.quantity,' ',p.unit,')') SEPARATOR '; ') products FROM freights f JOIN carriers c ON c.id=f.carrier_id JOIN addresses o ON o.id=f.origin_id JOIN addresses d ON d.id=f.destination_id LEFT JOIN freight_products fp ON fp.freight_id=f.id LEFT JOIN products p ON p.id=fp.product_id WHERE f.freight_date=? GROUP BY f.id ORDER BY f.id");
$q->execute([$date]);$rows=$q->fetchAll();
if(isset($_GET['export'])&&$_GET['export']==='csv'){
 header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="relatorio_fretes_'.$date.'.csv"');
 $out=fopen('php://output','w');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,['Número','Data','Transportador','Origem','Destino','Produtos','Situação'],';');
 foreach($rows as $r)fputcsv($out,[$r['id'],$r['freight_date'],$r['carrier'],$r['origin'],$r['destination'],$r['products'],$r['status']],';');
 fclose($out);exit;
}
require 'includes/header.php';
?>
<div class="page-heading"><div><h1>Relatórios</h1><p>Consulte e baixe os fretes de um determinado dia.</p></div></div>
<div class="card mb-4"><div class="card-body"><form class="row g-3 align-items-end" method="get"><div class="col-md-4"><label class="form-label">Data do relatório</label><input type="date" name="date" class="form-control" value="<?= e($date) ?>" required></div><div class="col-auto"><button class="btn btn-primary">Buscar</button></div><div class="col-auto"><a class="btn btn-outline-primary" href="?date=<?= e($date) ?>&export=csv">Baixar relatório CSV</a></div></form></div></div>
<div class="card"><div class="card-header d-flex justify-content-between"><span>Fretes em <?= date('d/m/Y',strtotime($date)) ?></span><span class="badge badge-soft"><?= count($rows) ?> registro(s)</span></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Nº</th><th>Transportador</th><th>Origem</th><th>Destino</th><th>Produtos</th><th>Situação</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="6" class="empty-state">Não há fretes cadastrados para esta data.</td></tr><?php endif; foreach($rows as $r): ?><tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['carrier']) ?></td><td><?= e($r['origin']) ?></td><td><?= e($r['destination']) ?></td><td><?= e($r['products']) ?></td><td><span class="badge badge-soft"><?= e($r['status']) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
