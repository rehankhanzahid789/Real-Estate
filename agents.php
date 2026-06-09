<?php
require_once 'includes/functions.php';
$page='agents'; $page_title='Agents — Billah Dee King';
$agents = db()->query('SELECT * FROM agents ORDER BY id')->fetchAll();
include 'includes/header.php';
?>
<section class="page-head">
  <div class="container">
    <span class="eyebrow">Meet the Team</span>
    <h1>Our advisors</h1>
    <p class="muted" style="max-width:640px">A small, deliberately chosen team of specialists with deep knowledge of the cities and asset classes they represent.</p>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="grid grid-3">
      <?php foreach ($agents as $a): ?>
        <div class="agent-card" data-reveal>
          <div class="ph" style="background-image:url('<?= e($a['photo']) ?>')"></div>
          <div class="info">
            <h3><?= e($a['name']) ?></h3>
            <div class="role"><?= e($a['role']) ?></div>
            <p class="bio"><?= e($a['bio']) ?></p>
            <p class="muted" style="margin-top:14px;font-size:.85rem"><?= e($a['email']) ?><br><?= e($a['phone']) ?></p>
            <div class="socials">
              <a href="<?= e($a['facebook']) ?>">Facebook</a>
              <a href="<?= e($a['instagram']) ?>">Instagram</a>
              <a href="<?= e($a['linkedin']) ?>">LinkedIn</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
