<?php
$page_title='Users';
require __DIR__.'/_header.php';

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
    if (isset($_POST['delete'])) {
        $uid = (int)$_POST['delete'];
        if ($uid !== (int)current_user()['id']) db()->prepare('DELETE FROM users WHERE id=?')->execute([$uid]);
    }
    if (isset($_POST['set_role'])) {
        db()->prepare('UPDATE users SET role=? WHERE id=?')->execute([$_POST['role'], (int)$_POST['set_role']]);
    }
    header('Location: users.php'); exit;
}
$rows = db()->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
?>
<h1>Users</h1><div class="divider-gold"></div>
<div class="dash-card" style="margin-top:20px">
<table class="table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Verified</th><th>Joined</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
  <tr>
    <td><?= e($r['name']) ?></td>
    <td><?= e($r['email']) ?></td>
    <td>
      <form method="post" style="display:inline-flex"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <select name="role" onchange="this.form.submit()"><option value="user" <?= $r['role']==='user'?'selected':'' ?>>user</option><option value="admin" <?= $r['role']==='admin'?'selected':'' ?>>admin</option></select>
        <input type="hidden" name="set_role" value="<?= (int)$r['id'] ?>">
      </form>
    </td>
    <td><?= $r['is_verified']?'✓':'—' ?></td>
    <td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td>
    <td style="text-align:right">
      <?php if ((int)$r['id'] !== (int)current_user()['id']): ?>
      <form method="post" onsubmit="return confirm('Delete user?')" style="display:inline"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><button name="delete" value="<?= (int)$r['id'] ?>" class="btn btn-ghost" style="color:#c0392b">Delete</button></form>
      <?php endif; ?>
    </td>
  </tr>
<?php endforeach; ?>
</tbody></table>
</div>
<?php require __DIR__.'/_footer.php'; ?>
