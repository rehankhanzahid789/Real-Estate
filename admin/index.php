<?php
$page_title='Admin Overview';
require __DIR__.'/_header.php';
$counts = [
  'Properties' => db()->query('SELECT COUNT(*) FROM properties')->fetchColumn(),
  'Agents'     => db()->query('SELECT COUNT(*) FROM agents')->fetchColumn(),
  'Users'      => db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
  'Inquiries'  => db()->query('SELECT COUNT(*) FROM inquiries')->fetchColumn(),
];
$recent = db()->query('SELECT i.*, p.title FROM inquiries i JOIN properties p ON p.id=i.property_id ORDER BY i.created_at DESC LIMIT 8')->fetchAll();
?>
<h1>Overview</h1><div class="divider-gold"></div>
<div class="kpi-row" style="margin-top:30px">
  <?php foreach ($counts as $l=>$n): ?>
    <div class="kpi"><div class="n"><?= (int)$n ?></div><div class="l"><?= e($l) ?></div></div>
  <?php endforeach; ?>
</div>
<div class="dash-card">
  <h3 style="margin-bottom:18px">Recent inquiries</h3>
  <table class="table">
    <thead><tr><th>From</th><th>Email</th><th>Property</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>
    <?php foreach ($recent as $r): ?>
      <tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><a href="../property-details.php?id=<?= (int)$r['property_id'] ?>"><?= e($r['title']) ?></a></td><td><?= e($r['status']) ?></td><td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__.'/_footer.php'; ?>
