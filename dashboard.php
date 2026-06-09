<?php
require_once 'includes/functions.php';
require_login();
$u = current_user();
$page='dashboard'; $page_title='Dashboard — Billah Dee King';
$tab = $_GET['tab'] ?? 'overview';
$msg=null;

// favorite toggle / remove
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
    if (isset($_POST['remove_fav'])) {
        db()->prepare('DELETE FROM favorites WHERE user_id=? AND property_id=?')->execute([$u['id'], (int)$_POST['remove_fav']]);
        $msg = 'Removed from favorites.';
    }
    if (isset($_POST['update_profile'])) {
        $n=trim($_POST['name']??''); $ph=trim($_POST['phone']??'');
        if ($n!=='') {
            db()->prepare('UPDATE users SET name=?, phone=? WHERE id=?')->execute([$n,$ph,$u['id']]);
            $msg='Profile updated.';
            $u = current_user();
        }
    }
}

$favs = db()->prepare('SELECT p.* FROM favorites f JOIN properties p ON p.id=f.property_id WHERE f.user_id=? ORDER BY f.created_at DESC');
$favs->execute([$u['id']]); $favs = $favs->fetchAll();

$inqs = db()->prepare('SELECT i.*, p.title FROM inquiries i JOIN properties p ON p.id=i.property_id WHERE i.user_id=? ORDER BY i.created_at DESC');
$inqs->execute([$u['id']]); $inqs = $inqs->fetchAll();

include 'includes/header.php'; ?>
<section class="page-head">
  <div class="container">
    <span class="eyebrow">Account</span>
    <h1>Welcome, <?= e(explode(' ',$u['name'])[0]) ?></h1>
  </div>
</section>
<section class="section-tight">
  <div class="container">
    <?php if ($msg): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
    <div class="dash-wrap">
      <aside class="dash-side">
        <a href="?tab=overview"  class="<?= $tab==='overview'?'active':'' ?>">Overview</a>
        <a href="?tab=favorites" class="<?= $tab==='favorites'?'active':'' ?>">Favorites</a>
        <a href="?tab=inquiries" class="<?= $tab==='inquiries'?'active':'' ?>">My Inquiries</a>
        <a href="?tab=profile"   class="<?= $tab==='profile'?'active':'' ?>">Profile</a>
        <a href="logout.php">Sign out</a>
      </aside>
      <div>
        <?php if ($tab==='overview'): ?>
          <div class="kpi-row">
            <div class="kpi"><div class="n"><?= count($favs) ?></div><div class="l">Saved homes</div></div>
            <div class="kpi"><div class="n"><?= count($inqs) ?></div><div class="l">Inquiries</div></div>
            <div class="kpi"><div class="n"><?= date('M Y', strtotime($u['created_at'])) ?></div><div class="l">Member since</div></div>
            <div class="kpi"><div class="n">VIP</div><div class="l">Status</div></div>
          </div>
          <div class="dash-card">
            <h3>Continue exploring</h3>
            <p class="muted" style="margin:8px 0 18px">Browse our curated collection of luxury residences.</p>
            <a href="properties.php" class="btn btn-gold">View properties</a>
          </div>
        <?php elseif ($tab==='favorites'): ?>
          <div class="dash-card">
            <h3 style="margin-bottom:18px">Saved properties</h3>
            <?php if (!$favs): ?><p class="muted">You haven't saved any properties yet.</p><?php else: ?>
              <div class="grid grid-2">
                <?php foreach ($favs as $p): ?>
                  <div class="property-card">
                    <a href="property-details.php?id=<?= (int)$p['id'] ?>" class="img"><img src="<?= e(property_main_image($p['id'])) ?>" alt=""><span class="price"><?= format_price($p['price']) ?></span></a>
                    <div class="body">
                      <h3><?= e($p['title']) ?></h3>
                      <div class="loc"><?= e($p['city']) ?></div>
                      <form method="post" style="margin-top:14px">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <button name="remove_fav" value="<?= (int)$p['id'] ?>" class="btn btn-outline" style="font-size:.78rem;padding:8px 16px">Remove</button>
                      </form>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php elseif ($tab==='inquiries'): ?>
          <div class="dash-card">
            <h3 style="margin-bottom:18px">Your inquiries</h3>
            <?php if (!$inqs): ?><p class="muted">No inquiries yet.</p><?php else: ?>
              <table class="table">
                <thead><tr><th>Property</th><th>Sent</th><th>Status</th></tr></thead>
                <tbody>
                  <?php foreach ($inqs as $i): ?>
                    <tr><td><a href="property-details.php?id=<?= (int)$i['property_id'] ?>"><?= e($i['title']) ?></a></td>
                        <td><?= e(date('M j, Y', strtotime($i['created_at']))) ?></td>
                        <td><?= e(ucfirst($i['status'])) ?></td></tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        <?php elseif ($tab==='profile'): ?>
          <div class="dash-card">
            <h3 style="margin-bottom:18px">Edit profile</h3>
            <form method="post" style="max-width:480px">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="update_profile" value="1">
              <div class="form-row"><label>Name</label><input name="name" value="<?= e($u['name']) ?>" required></div>
              <div class="form-row"><label>Email</label><input value="<?= e($u['email']) ?>" disabled></div>
              <div class="form-row"><label>Phone</label><input name="phone" value="<?= e($u['phone']) ?>"></div>
              <button class="btn btn-gold">Save changes</button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
