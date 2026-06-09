<?php
require_once 'includes/functions.php';
$page='properties'; $page_title='Properties — Billah Dee King';

$city = trim($_GET['city'] ?? '');
$type = trim($_GET['type'] ?? '');
$beds = (int)($_GET['beds'] ?? 0);
$baths = (int)($_GET['baths'] ?? 0);
$pmin = (float)($_GET['price_min'] ?? 0);
$pmax = (float)($_GET['price_max'] ?? 0);
$smin = (int)($_GET['size_min'] ?? 0);

$where = []; $params = [];
if ($city !== '') { $where[]='city LIKE ?'; $params[]="%$city%"; }
if ($type !== '') { $where[]='property_type = ?'; $params[]=$type; }
if ($beds)       { $where[]='bedrooms >= ?'; $params[]=$beds; }
if ($baths)      { $where[]='bathrooms >= ?'; $params[]=$baths; }
if ($pmin>0)     { $where[]='price >= ?'; $params[]=$pmin; }
if ($pmax>0)     { $where[]='price <= ?'; $params[]=$pmax; }
if ($smin>0)     { $where[]='size_sqft >= ?'; $params[]=$smin; }
$where_sql = $where ? ' WHERE '.implode(' AND ',$where) : '';

$per = 9;
$page_num = max(1,(int)($_GET['p'] ?? 1));
$offset = ($page_num-1)*$per;

$count_stmt = db()->prepare("SELECT COUNT(*) FROM properties $where_sql");
$count_stmt->execute($params);
$total = (int)$count_stmt->fetchColumn();
$pages = max(1, (int)ceil($total/$per));

$stmt = db()->prepare("SELECT * FROM properties $where_sql ORDER BY is_featured DESC, created_at DESC LIMIT $per OFFSET $offset");
$stmt->execute($params);
$rows = $stmt->fetchAll();

include 'includes/header.php';
?>
<section class="page-head">
  <div class="container">
    <span class="eyebrow">Curated Collection</span>
    <h1>Properties</h1>
    <p class="muted"><?= (int)$total ?> residences match your selection.</p>
  </div>
</section>

<section class="section-tight">
  <div class="container">
    <form class="filters" method="get">
      <div class="form-row"><label>City</label><input type="text" name="city" value="<?= e($city) ?>" placeholder="Any" /></div>
      <div class="form-row"><label>Type</label>
        <select name="type">
          <option value="">Any</option>
          <?php foreach(['villa','penthouse','apartment','house','estate','townhouse'] as $t): ?>
            <option value="<?= $t ?>" <?= $type===$t?'selected':'' ?>><?= ucfirst($t) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row"><label>Beds</label>
        <select name="beds">
          <option value="">Any</option>
          <?php for($i=1;$i<=7;$i++): ?><option value="<?= $i ?>" <?= $beds===$i?'selected':'' ?>><?= $i ?>+</option><?php endfor; ?>
        </select>
      </div>
      <div class="form-row"><label>Baths</label>
        <select name="baths">
          <option value="">Any</option>
          <?php for($i=1;$i<=7;$i++): ?><option value="<?= $i ?>" <?= $baths===$i?'selected':'' ?>><?= $i ?>+</option><?php endfor; ?>
        </select>
      </div>
      <div class="form-row"><label>Min price</label><input type="number" name="price_min" value="<?= $pmin?:'' ?>" placeholder="$" /></div>
      <div class="form-row"><label>Max price</label><input type="number" name="price_max" value="<?= $pmax?:'' ?>" placeholder="$" /></div>
      <div class="form-row" style="grid-column:span 6;display:flex;gap:10px;justify-content:flex-end">
        <a href="properties.php" class="btn btn-ghost">Reset</a>
        <button class="btn btn-gold">Apply filters</button>
      </div>
    </form>

    <?php if (!$rows): ?>
      <div class="alert">No properties match your filters. Try widening your search.</div>
    <?php else: ?>
    <div class="grid grid-3">
      <?php foreach ($rows as $p): ?>
        <a class="property-card" href="property-details.php?id=<?= (int)$p['id'] ?>">
          <div class="img">
            <img src="<?= e(property_main_image($p['id'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy" />
            <span class="tag"><?= e(ucfirst(str_replace('-',' ',$p['status']))) ?></span>
            <span class="price"><?= format_price($p['price']) ?></span>
          </div>
          <div class="body">
            <h3><?= e($p['title']) ?></h3>
            <div class="loc"><?= e($p['city']) ?> · <?= e(ucfirst($p['property_type'])) ?></div>
            <div class="meta">
              <span><strong><?= (int)$p['bedrooms'] ?></strong> Beds</span>
              <span><strong><?= (int)$p['bathrooms'] ?></strong> Baths</span>
              <span><strong><?= number_format($p['size_sqft']) ?></strong> sqft</span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
      <div class="pagination">
        <?php
          $qs = $_GET; unset($qs['p']);
          for ($i=1;$i<=$pages;$i++):
            $qs['p']=$i;
            $url='properties.php?'.http_build_query($qs);
        ?>
          <?php if ($i===$page_num): ?><span class="active"><?= $i ?></span>
          <?php else: ?><a href="<?= e($url) ?>"><?= $i ?></a><?php endif; ?>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
