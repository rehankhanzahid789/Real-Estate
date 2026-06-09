<?php
$page_title='Manage Properties';
require __DIR__.'/_header.php';

$action = $_GET['action'] ?? 'list';
$msg=$err=null;

// Delete
if ($action==='delete' && csrf_check($_GET['csrf']??'')) {
    $id=(int)$_GET['id'];
    db()->prepare('DELETE FROM properties WHERE id=?')->execute([$id]);
    header('Location: properties.php?ok=Deleted'); exit;
}

// Save (create or update)
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
    $id = (int)($_POST['id'] ?? 0);
    $data = [
      trim($_POST['title']??''),
      preg_replace('/[^a-z0-9-]/','', strtolower(str_replace(' ','-', $_POST['title']??''))).'-'.uniqid(),
      trim($_POST['description']??''),
      (float)($_POST['price']??0),
      trim($_POST['city']??''),
      trim($_POST['address']??''),
      $_POST['property_type']??'villa',
      $_POST['status']??'for-sale',
      (int)($_POST['bedrooms']??0),
      (int)($_POST['bathrooms']??0),
      (int)($_POST['size_sqft']??0),
      trim($_POST['amenities']??''),
      (int)($_POST['agent_id']??0) ?: null,
      isset($_POST['is_featured']) ? 1 : 0,
    ];
    if ($id) {
        // keep slug
        array_splice($data,1,1);
        $sql='UPDATE properties SET title=?,description=?,price=?,city=?,address=?,property_type=?,status=?,bedrooms=?,bathrooms=?,size_sqft=?,amenities=?,agent_id=?,is_featured=? WHERE id=?';
        $data[]=$id;
        db()->prepare($sql)->execute($data);
        $pid=$id;
    } else {
        $sql='INSERT INTO properties (title,slug,description,price,city,address,property_type,status,bedrooms,bathrooms,size_sqft,amenities,agent_id,is_featured) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        db()->prepare($sql)->execute($data);
        $pid=(int)db()->lastInsertId();
    }
    // image upload
    if (!empty($_FILES['images']['name'][0])) {
        $dir = __DIR__ . '/../uploads/properties/';
        if (!is_dir($dir)) @mkdir($dir,0775,true);
        foreach ($_FILES['images']['tmp_name'] as $k=>$tmp) {
            if (!is_uploaded_file($tmp)) continue;
            $ext = strtolower(pathinfo($_FILES['images']['name'][$k], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;
            $fn = 'p'.$pid.'-'.uniqid().'.'.$ext;
            if (@move_uploaded_file($tmp, $dir.$fn)) {
                $rel = 'uploads/properties/'.$fn;
                db()->prepare('INSERT INTO property_images (property_id,image_path,is_primary) VALUES (?,?,0)')->execute([$pid,$rel]);
            }
        }
    }
    header('Location: properties.php?ok=Saved'); exit;
}

if ($action==='edit' || $action==='new') {
    $p = ['id'=>0,'title'=>'','description'=>'','price'=>'','city'=>'','address'=>'','property_type'=>'villa','status'=>'for-sale','bedrooms'=>0,'bathrooms'=>0,'size_sqft'=>0,'amenities'=>'','agent_id'=>'','is_featured'=>0];
    if ($action==='edit') {
        $st=db()->prepare('SELECT * FROM properties WHERE id=?'); $st->execute([(int)$_GET['id']]);
        $p = $st->fetch() ?: $p;
    }
    $agents = db()->query('SELECT id,name FROM agents ORDER BY name')->fetchAll();
    $imgs = $p['id'] ? property_images($p['id']) : [];
    ?>
    <h1><?= $p['id']?'Edit':'New' ?> property</h1><div class="divider-gold"></div>
    <form method="post" enctype="multipart/form-data" class="dash-card" style="margin-top:24px">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
      <div class="form-grid-2">
        <div class="form-row"><label>Title</label><input name="title" required value="<?= e($p['title']) ?>"></div>
        <div class="form-row"><label>Price (USD)</label><input type="number" step="0.01" name="price" required value="<?= e($p['price']) ?>"></div>
        <div class="form-row"><label>City</label><input name="city" required value="<?= e($p['city']) ?>"></div>
        <div class="form-row"><label>Address</label><input name="address" value="<?= e($p['address']) ?>"></div>
        <div class="form-row"><label>Type</label><select name="property_type">
          <?php foreach(['villa','penthouse','apartment','house','estate','townhouse'] as $t): ?>
            <option value="<?= $t ?>" <?= $p['property_type']===$t?'selected':'' ?>><?= ucfirst($t) ?></option>
          <?php endforeach; ?>
        </select></div>
        <div class="form-row"><label>Status</label><select name="status">
          <?php foreach(['for-sale','for-rent','sold'] as $s): ?>
            <option value="<?= $s ?>" <?= $p['status']===$s?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select></div>
        <div class="form-row"><label>Bedrooms</label><input type="number" name="bedrooms" value="<?= (int)$p['bedrooms'] ?>"></div>
        <div class="form-row"><label>Bathrooms</label><input type="number" name="bathrooms" value="<?= (int)$p['bathrooms'] ?>"></div>
        <div class="form-row"><label>Size (sqft)</label><input type="number" name="size_sqft" value="<?= (int)$p['size_sqft'] ?>"></div>
        <div class="form-row"><label>Agent</label><select name="agent_id">
          <option value="">— None —</option>
          <?php foreach ($agents as $a): ?><option value="<?= (int)$a['id'] ?>" <?= (int)$p['agent_id']===(int)$a['id']?'selected':'' ?>><?= e($a['name']) ?></option><?php endforeach; ?>
        </select></div>
      </div>
      <div class="form-row"><label>Amenities (comma separated)</label><input name="amenities" value="<?= e($p['amenities']) ?>"></div>
      <div class="form-row"><label>Description</label><textarea name="description" rows="6"><?= e($p['description']) ?></textarea></div>
      <div class="form-row"><label><input type="checkbox" name="is_featured" <?= $p['is_featured']?'checked':'' ?>> Featured on homepage</label></div>
      <div class="form-row"><label>Add images</label><input type="file" name="images[]" multiple accept="image/*"></div>
      <?php if ($imgs): ?>
        <div class="form-row"><label>Current images</label>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            <?php foreach ($imgs as $im): ?>
              <img src="<?= e(strpos($im['image_path'],'http')===0 ? $im['image_path'] : '../'.$im['image_path']) ?>" style="width:120px;height:90px;object-fit:cover;border:1px solid #eee" alt="">
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
      <div style="display:flex;gap:10px"><button class="btn btn-gold">Save property</button><a href="properties.php" class="btn btn-ghost">Cancel</a></div>
    </form>
    <?php require __DIR__.'/_footer.php'; exit;
}

// List
$rows = db()->query('SELECT p.*, a.name AS agent_name FROM properties p LEFT JOIN agents a ON a.id=p.agent_id ORDER BY p.created_at DESC')->fetchAll();
?>
<div style="display:flex;justify-content:space-between;align-items:center"><h1>Properties</h1><a class="btn btn-gold" href="properties.php?action=new">+ New property</a></div><div class="divider-gold"></div>
<?php if (!empty($_GET['ok'])): ?><div class="alert success" style="margin-top:20px"><?= e($_GET['ok']) ?></div><?php endif; ?>
<div class="dash-card" style="margin-top:20px">
  <table class="table">
    <thead><tr><th>Title</th><th>City</th><th>Type</th><th>Price</th><th>Agent</th><th>Featured</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= e($r['title']) ?></td>
        <td><?= e($r['city']) ?></td>
        <td><?= e($r['property_type']) ?></td>
        <td><?= format_price($r['price']) ?></td>
        <td><?= e($r['agent_name'] ?? '—') ?></td>
        <td><?= $r['is_featured']?'★':'' ?></td>
        <td style="text-align:right">
          <a href="properties.php?action=edit&id=<?= (int)$r['id'] ?>">Edit</a> ·
          <a href="properties.php?action=delete&id=<?= (int)$r['id'] ?>&csrf=<?= e(csrf_token()) ?>" onclick="return confirm('Delete this property?')" style="color:#c0392b">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__.'/_footer.php'; ?>
