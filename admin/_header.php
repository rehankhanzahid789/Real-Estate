<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$u = current_user();
$page_title = $page_title ?? 'Admin — Billah Dee King';
$current = basename($_SERVER['PHP_SELF']);
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head><body>
<header class="site-header"><div class="container nav-wrap">
  <a class="brand" href="../index.php"><img src="../assets/images/logo.png" alt="Billah Dee King" class="brand-logo" /></a>
  <nav class="main-nav">
    <a href="index.php"      class="<?= $current==='index.php'?'active':'' ?>">Overview</a>
    <a href="properties.php" class="<?= $current==='properties.php'?'active':'' ?>">Properties</a>
    <a href="agents.php"     class="<?= $current==='agents.php'?'active':'' ?>">Agents</a>
    <a href="inquiries.php"  class="<?= $current==='inquiries.php'?'active':'' ?>">Inquiries</a>
    <a href="users.php"      class="<?= $current==='users.php'?'active':'' ?>">Users</a>
  </nav>
  <div class="nav-cta">
    <a href="../index.php" class="btn btn-ghost">View site</a>
    <a href="../logout.php" class="btn btn-gold">Logout</a>
  </div>
</div></header>
<main class="section-tight"><div class="container">
