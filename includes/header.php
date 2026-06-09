<?php
require_once __DIR__ . '/functions.php';
$u = current_user();
$page = $page ?? 'home';
$page_title = $page_title ?? 'Billah Dee King — Premium Real Estate';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= e($page_title) ?></title>
<meta name="description" content="Billah Dee King — discover premium luxury homes, villas and penthouses curated for discerning buyers." />
<link rel="stylesheet" href="assets/css/style.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="index.php">
      <img src="assets/images/logo.png" alt="Billah Dee King" class="brand-logo" />
    </a>
    <nav class="main-nav">
      <a href="index.php" class="<?= $page==='home'?'active':'' ?>">Home</a>
      <a href="properties.php" class="<?= $page==='properties'?'active':'' ?>">Properties</a>
      <a href="agents.php" class="<?= $page==='agents'?'active':'' ?>">Agents</a>
      <a href="about.php" class="<?= $page==='about'?'active':'' ?>">About</a>
      <a href="contact.php" class="<?= $page==='contact'?'active':'' ?>">Contact</a>
    </nav>
    <div class="nav-cta">
      <?php if ($u): ?>
        <a href="dashboard.php" class="btn btn-ghost">Dashboard</a>
        <?php if ($u['role']==='admin'): ?><a href="admin/index.php" class="btn btn-ghost">Admin</a><?php endif; ?>
        <a href="logout.php" class="btn btn-gold">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-ghost">Sign in</a>
        <a href="register.php" class="btn btn-gold">Join</a>
      <?php endif; ?>
    </div>
    <button class="nav-toggle" aria-label="Menu" onclick="document.body.classList.toggle('nav-open')">☰</button>
  </div>
</header>
<main>
