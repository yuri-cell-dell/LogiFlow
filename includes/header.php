<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Painel';
$current = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | LogiFlow</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark topbar">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold" href="index.php"><span class="brand-mark">L</span> LOGIFLOW</a>
    <span class="text-white-50 small d-none d-sm-inline">Gerenciador Logístico</span>
  </div>
</nav>
<div class="app-shell">
  <aside class="sidebar">
    <div class="sidebar-label">MENU PRINCIPAL</div>
    <a class="<?= $current==='index.php'?'active':'' ?>" href="index.php">▦ <span>Visão geral</span></a>
    <a class="<?= $current==='products.php'?'active':'' ?>" href="products.php">▣ <span>Produtos</span></a>
    <a class="<?= $current==='addresses.php'?'active':'' ?>" href="addresses.php">⌖ <span>Endereços</span></a>
    <a class="<?= $current==='carriers.php'?'active':'' ?>" href="carriers.php">▤ <span>Transportadores</span></a>
    <a class="<?= $current==='freights.php'?'active':'' ?>" href="freights.php">⇄ <span>Fretes</span></a>
    <a class="<?= $current==='reports.php'?'active':'' ?>" href="reports.php">▧ <span>Relatórios</span></a>
    <div class="sidebar-foot">Projeto acadêmico • UNIPAR EAD</div>
  </aside>
  <main class="main-content">
    <div class="content-wrap">
      <?php show_flash(); ?>
