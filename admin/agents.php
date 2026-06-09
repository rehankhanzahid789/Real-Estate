<?php
$page_title='Manage Agents';
require __DIR__.'/_header.php';
$action=$_GET['action']??'list';

if ($action==='delete' && csrf_check($_GET['csrf']??'')) {
    db()->prepare('DELETE FROM agents WHERE id=?')->execute([(int)$_GET['id']]);
    header('Location: agents.php?ok=Deleted'); exit;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
    $id=(int)($_POST['id']??0);
    $vals = [trim($_POST['name']??''),trim($_POST['role']??''),trim($_POST['email']??''),trim($_POST['phone']??''),trim($_POST['bio']??''),trim($_POST['photo']??''),trim($_POST['facebook']??''),trim($_POST['instagram']??''),trim($_POST['linkedin']??'')];
    if ($id) { $vals[]=$id; db()->prepare('UPDATE agents SET name=?,role=?,email=?,phone=?,bio=?,photo=?,facebook=?,instagram=?,linkedin=? WHERE id=?')->execute($vals); }
    else    { db()->prepare('INSERT INTO agents (name,role,email,phone,bio,photo,facebook,instagram,linkedin) VALUES (?,?,?,?,?,?,?,?,?)')->execute($vals); }
    header('Location: agents.php?ok=Saved'); exit;
}
if ($action==='edit'||$action==='new') {
    $a=['id'=>0,'name'=>'','role'=>'Senior Advisor','email'=>'','phone'=>'','bio'=>'','photo'=>'','facebook'=>'','instagram'=>'','linkedin'=>''];
    if ($action==='edit') { $st=db()->prepare('SELECT * FROM agents WHERE id=?');$st->execute([(int)$_GET['id']]); $a=$st->fetch()?:$a; }
    ?>
    <h1><?= $a['id']?'Edit':'New' ?> agent</h1><div class="divider-gold"></div>
    <form method="post" class="dash-card" style="margin-top:20px">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
      <div class="form-grid-2">
        <div class="form-row"><label>Name</label><input name="name" required value="<?= e($a['name']) ?>"></div>
        <div class="form-row"><label>Role</label><input name="role" value="<?= e($a['role']) ?>"></div>
        <div class="form-row"><label>Email</label><input type="email" name="email" value="<?= e($a['email']) ?>"></div>
        <div class="form-row"><label>Phone</label><input name="phone" value="<?= e($a['phone']) ?>"></div>
        <div class="form-row"><label>Photo URL</label><input name="photo" value="<?= e($a['photo']) ?>"></div>
        <div class="form-row"><label>LinkedIn</label><input name="linkedin" value="<?= e($a['linkedin']) ?>"></div>
        <div class="form-row"><label>Instagram</label><input name="instagram" value="<?= e($a['instagram']) ?>"></div>
        <div class="form-row"><label>Facebook</label><input name="facebook" value="<?= e($a['facebook']) ?>"></div>
      </div>
      <div class="form-row"><label>Bio</label><textarea name="bio" rows="5"><?= e($a['bio']) ?></textarea></div>
      <div style="display:flex;gap:10px"><button class="btn btn-gold">Save</button><a href="agents.php" class="btn btn-ghost">Cancel</a></div>
    </form>
    <?php require __DIR__.'/_footer.php'; exit;
}
$rows = db()->query('SELECT * FROM agents ORDER BY name')->fetchAll();
?>
<div style="display:flex;justify-content:space-between"><h1>Agents</h1><a class="btn btn-gold" href="agents.php?action=new">+ New agent</a></div><div class="divider-gold"></div>
<?php if (!empty($_GET['ok'])): ?><div class="alert success" style="margin-top:20px"><?= e($_GET['ok']) ?></div><?php endif; ?>
<div class="dash-card" style="margin-top:20px">
<table class="table"><thead><tr><th>Name</th><th>Role</th><th>Email</th><th>Phone</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
  <tr><td><?= e($r['name']) ?></td><td><?= e($r['role']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['phone']) ?></td>
    <td style="text-align:right"><a href="agents.php?action=edit&id=<?= (int)$r['id'] ?>">Edit</a> · <a href="agents.php?action=delete&id=<?= (int)$r['id'] ?>&csrf=<?= e(csrf_token()) ?>" onclick="return confirm('Delete?')" style="color:#c0392b">Delete</a></td>
  </tr>
<?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__.'/_footer.php'; ?>
