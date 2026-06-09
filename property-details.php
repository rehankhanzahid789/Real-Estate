<?php
require_once 'includes/functions.php';
$page='properties'; $id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT p.*, a.name AS agent_name, a.email AS agent_email, a.phone AS agent_phone, a.photo AS agent_photo, a.role AS agent_role FROM properties p LEFT JOIN agents a ON a.id=p.agent_id WHERE p.id=?');
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { http_response_code(404); echo 'Property not found.'; exit; }
$page_title = $p['title'].' — Billah Dee King';
$images = property_images($p['id']);
$main_img = $images[0]['image_path'] ?? 'assets/images/placeholder.jpg';
$amen = array_filter(array_map('trim', explode(',', $p['amenities'] ?? '')));

// inquiry
$err=$ok=null;
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf'] ?? '')) {
    $n=trim($_POST['name']??''); $em=trim($_POST['email']??''); $ph=trim($_POST['phone']??''); $m=trim($_POST['message']??'');
    if ($n===''||$em===''||$m==='') $err='Please complete the form.';
    elseif (!filter_var($em,FILTER_VALIDATE_EMAIL)) $err='Invalid email.';
    else {
        $uid = current_user()['id'] ?? null;
        db()->prepare('INSERT INTO inquiries (property_id,user_id,name,email,phone,message) VALUES (?,?,?,?,?,?)')
            ->execute([$p['id'],$uid,$n,$em,$ph,$m]);
        $ok='Thank you — your inquiry has been sent. An advisor will be in touch shortly.';
    }
}

$similar = db()->prepare('SELECT * FROM properties WHERE property_type=? AND id<>? ORDER BY RAND() LIMIT 3');
$similar->execute([$p['property_type'],$p['id']]);
$similar = $similar->fetchAll();

include 'includes/header.php';
?>
<section class="section-tight">
  <div class="container">
    <div class="gallery">
      <div class="main" style="background-image:url('<?= e($main_img) ?>')"></div>
      <?php for ($i=1;$i<5;$i++): if (!isset($images[$i])) break; ?>
        <div class="thumb" style="background-image:url('<?= e($images[$i]['image_path']) ?>');cursor:pointer"></div>
      <?php endfor; ?>
    </div>

    <div class="detail-grid">
      <div>
        <span class="eyebrow"><?= e(ucfirst($p['property_type'])) ?> · <?= e(ucfirst(str_replace('-',' ',$p['status']))) ?></span>
        <h1><?= e($p['title']) ?></h1>
        <div class="loc"><?= e($p['address']) ?>, <?= e($p['city']) ?></div>
        <div style="font-family:'Cormorant Garamond',serif;font-size:2.4rem;color:var(--gold);font-weight:600"><?= format_price($p['price']) ?></div>

        <div class="detail-stats">
          <div><strong><?= (int)$p['bedrooms'] ?></strong>Bedrooms</div>
          <div><strong><?= (int)$p['bathrooms'] ?></strong>Bathrooms</div>
          <div><strong><?= number_format($p['size_sqft']) ?></strong>Sq Ft</div>
          <div><strong><?= e(ucfirst($p['property_type'])) ?></strong>Type</div>
        </div>

        <h3>About this residence</h3>
        <div class="divider-gold"></div>
        <p><?= nl2br(e($p['description'])) ?></p>

        <?php if ($amen): ?>
          <h3 style="margin-top:36px">Amenities</h3>
          <div class="divider-gold"></div>
          <ul class="amenities">
            <?php foreach ($amen as $a): ?><li><?= e($a) ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <h3 style="margin-top:36px">Location</h3>
        <div class="divider-gold"></div>
        <div class="map-placeholder">Map preview · <?= e($p['city']) ?></div>
      </div>

      <aside>
        <div class="sidebar-card">
          <?php if ($p['agent_name']): ?>
          <div class="agent-mini">
            <img src="<?= e($p['agent_photo']) ?>" alt="" />
            <div><strong><?= e($p['agent_name']) ?></strong><span><?= e($p['agent_role']) ?></span></div>
          </div>
          <?php endif; ?>

          <h3 style="font-size:1.2rem;margin-bottom:6px">Request a private viewing</h3>
          <p class="muted" style="margin-bottom:18px;font-size:.88rem">An advisor will respond within one business day.</p>

          <?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div>
          <?php elseif ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>

          <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
            <div class="form-row"><label>Name</label><input name="name" required value="<?= e(current_user()['name'] ?? '') ?>"></div>
            <div class="form-row"><label>Email</label><input type="email" name="email" required value="<?= e(current_user()['email'] ?? '') ?>"></div>
            <div class="form-row"><label>Phone</label><input name="phone" value="<?= e(current_user()['phone'] ?? '') ?>"></div>
            <div class="form-row"><label>Message</label><textarea name="message" required>I'd like to arrange a viewing of <?= e($p['title']) ?>.</textarea></div>
            <button class="btn btn-gold btn-block">Send inquiry</button>
          </form>
        </div>
      </aside>
    </div>
  </div>
</section>

<?php if ($similar): ?>
<section class="section" style="background:var(--bg-2)">
  <div class="container">
    <div class="section-head"><div><span class="eyebrow">You may also like</span><h2>Similar residences</h2></div></div>
    <div class="grid grid-3">
      <?php foreach ($similar as $s): ?>
        <a class="property-card" href="property-details.php?id=<?= (int)$s['id'] ?>">
          <div class="img"><img src="<?= e(property_main_image($s['id'])) ?>" alt="" loading="lazy"><span class="price"><?= format_price($s['price']) ?></span></div>
          <div class="body">
            <h3><?= e($s['title']) ?></h3>
            <div class="loc"><?= e($s['city']) ?></div>
            <div class="meta"><span><strong><?= (int)$s['bedrooms'] ?></strong> Beds</span><span><strong><?= (int)$s['bathrooms'] ?></strong> Baths</span><span><strong><?= number_format($s['size_sqft']) ?></strong> sqft</span></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
