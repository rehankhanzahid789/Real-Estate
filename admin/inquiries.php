<?php
$page_title='Inquiries';
require __DIR__.'/_header.php';

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
    if (isset($_POST['delete'])) db()->prepare('DELETE FROM inquiries WHERE id=?')->execute([(int)$_POST['delete']]);
    if (isset($_POST['set_status'])) db()->prepare('UPDATE inquiries SET status=? WHERE id=?')->execute([$_POST['status'],(int)$_POST['set_status']]);
    header('Location: inquiries.php'); exit;
}
$rows = db()->query('SELECT i.*, p.title FROM inquiries i LEFT JOIN properties p ON p.id=i.property_id ORDER BY i.created_at DESC')->fetchAll();
$contact = db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
?>
<h1>Inquiries</h1><div class="divider-gold"></div>
<div class="dash-card" style="margin-top:20px">
<h3 style="margin-bottom:14px">Property inquiries</h3>
<table class="table"><thead><tr><th>From</th><th>Email</th><th>Property</th><th>Message</th><th>Status</th><th>Date</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
  <tr>
    <td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td>
    <td><?= e($r['title']) ?></td>
    <td style="max-width:280px"><?= e(mb_strimwidth($r['message'],0,140,'…')) ?></td>
    <td>
      <form method="post" style="display:inline-flex;gap:4px">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <select name="status" onchange="this.form.submit()">
          <?php foreach (['new','contacted','closed'] as $s): ?>
            <option value="<?= $s ?>" <?= $r['status']===$s?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="set_status" value="<?= (int)$r['id'] ?>">
      </form>
    </td>
    <td><?= e(date('M j', strtotime($r['created_at']))) ?></td>
    <td><form method="post" onsubmit="return confirm('Delete?')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><button name="delete" value="<?= (int)$r['id'] ?>" class="btn btn-ghost" style="color:#c0392b">×</button></form></td>
  </tr>
<?php endforeach; ?>
</tbody></table>
</div>

<div class="dash-card" style="margin-top:20px">
<h3 style="margin-bottom:14px">Contact form messages</h3>
<table class="table"><thead><tr><th>From</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th></tr></thead><tbody>
<?php foreach ($contact as $r): ?>
  <tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['subject']) ?></td><td style="max-width:320px"><?= e(mb_strimwidth($r['message'],0,160,'…')) ?></td><td><?= e(date('M j', strtotime($r['created_at']))) ?></td></tr>
<?php endforeach; ?>
</tbody></table>
</div>
<?php require __DIR__.'/_footer.php'; ?>
