<?php
require_once 'includes/functions.php';
$page = 'home';
$page_title = 'Billah Dee King — Premium Real Estate';

$featured = db()->query("SELECT * FROM properties WHERE is_featured=1 ORDER BY created_at DESC LIMIT 6")->fetchAll();
$hero = db()->query("SELECT p.*, (SELECT image_path FROM property_images WHERE property_id=p.id ORDER BY is_primary DESC LIMIT 1) AS img FROM properties p WHERE p.is_featured=1 ORDER BY p.price DESC LIMIT 1")->fetch();

$cats = [
  ['Penthouses','penthouse','https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80'],
  ['Villas','villa','https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&q=80'],
  ['Estates','estate','https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800&q=80'],
  ['Apartments','apartment','https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80'],
];
include 'includes/header.php';
?>
<section class="hero">
  <div class="hero-bg" style="background-image:url('<?= e($hero['img'] ?? 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1800&q=80') ?>')"></div>
  <div class="container">
    <div class="hero-inner" data-reveal>
      <span class="eyebrow">Curated Luxury · Since 2002</span>
      <h1>Homes that define<br/>a way of living.</h1>
      <p>From Park Avenue penthouses to seafront villas on the Côte d'Azur — discover residences worthy of a lifetime.</p>
      <div class="hero-actions">
        <a href="properties.php" class="btn btn-gold">Browse Properties</a>
        <a href="contact.php" class="btn btn-outline" style="color:#fff;border-color:#fff">Speak to an Advisor</a>
      </div>
    </div>
  </div>
</section>

<div class="container">
  <form class="search-bar" action="properties.php" method="get" data-reveal>
    <input type="text" name="city" placeholder="City or location" />
    <select name="type">
      <option value="">Property type</option>
      <option value="villa">Villa</option>
      <option value="penthouse">Penthouse</option>
      <option value="apartment">Apartment</option>
      <option value="house">House</option>
      <option value="estate">Estate</option>
      <option value="townhouse">Townhouse</option>
    </select>
    <select name="beds">
      <option value="">Bedrooms</option>
      <?php for($i=1;$i<=7;$i++) echo "<option value=$i>$i+</option>"; ?>
    </select>
    <select name="price_max">
      <option value="">Max price</option>
      <option value="5000000">Up to $5M</option>
      <option value="10000000">Up to $10M</option>
      <option value="20000000">Up to $20M</option>
      <option value="50000000">Up to $50M</option>
    </select>
    <button class="btn btn-gold" type="submit">Search</button>
  </form>
</div>

<section class="section">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <span class="eyebrow">Featured Listings</span>
        <h2>Hand-selected residences</h2>
      </div>
      <a href="properties.php" class="btn btn-outline">View all</a>
    </div>
    <div class="grid grid-3">
      <?php foreach ($featured as $p): ?>
        <a class="property-card" href="property-details.php?id=<?= (int)$p['id'] ?>" data-reveal>
          <div class="img">
            <img src="<?= e(property_main_image($p['id'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy" />
            <span class="tag"><?= e(ucfirst(str_replace('-',' ', $p['status']))) ?></span>
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
  </div>
</section>

<section class="section" style="background:var(--bg-2)">
  <div class="container">
    <div class="section-head center" style="justify-content:center;text-align:center;flex-direction:column" data-reveal>
      <div>
        <span class="eyebrow">Categories</span>
        <h2>Browse by residence type</h2>
      </div>
    </div>
    <div class="grid grid-4">
      <?php foreach ($cats as $c): ?>
        <a class="cat-card" href="properties.php?type=<?= e($c[1]) ?>" data-reveal>
          <img src="<?= e($c[2]) ?>" alt="<?= e($c[0]) ?>" loading="lazy" />
          <div class="label">
            <span class="count">Explore</span>
            <h3><?= e($c[0]) ?></h3>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container about-split">
    <div data-reveal>
      <span class="eyebrow">About Billah Dee King</span>
      <h2>Two decades representing the world's most refined homes.</h2>
      <div class="divider-gold"></div>
      <p>Billah Dee King is a privately-held brokerage representing trophy real estate across the globe. Our advisors combine quiet discretion with an unmatched network — connecting principals to the residences that rarely reach the open market.</p>
      <div class="stat-row">
        <div class="stat"><div class="n">$8.4B</div><div class="l">Lifetime Sales</div></div>
        <div class="stat"><div class="n">1,200+</div><div class="l">Homes Sold</div></div>
        <div class="stat"><div class="n">42</div><div class="l">Cities</div></div>
      </div>
    </div>
    <div class="about-img" style="background-image:url('https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=1000&q=80')" data-reveal></div>
  </div>
</section>

<section class="section testimonials">
  <div class="container">
    <div class="section-head center" style="justify-content:center;text-align:center;flex-direction:column" data-reveal>
      <div>
        <span class="eyebrow">Client Voices</span>
        <h2>Trusted by collectors of homes.</h2>
      </div>
    </div>
    <div class="grid grid-3">
      <div class="testimonial" data-reveal>
        <p>Isabella handled the entire transaction with extraordinary discretion. The closing was seamless and the result exceeded every expectation.</p>
        <div class="who"><img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80" alt=""><div><strong>Eleanor V.</strong><span>Manhattan, NY</span></div></div>
      </div>
      <div class="testimonial" data-reveal>
        <p>The team understood our brief instantly and brought us properties we would never have found ourselves. Truly best-in-class representation.</p>
        <div class="who"><img src="https://images.unsplash.com/photo-1633332755192-727a05c4013d?w=200&q=80" alt=""><div><strong>Marcus & Helena</strong><span>Cap Ferrat</span></div></div>
      </div>
      <div class="testimonial" data-reveal>
        <p>From the first private viewing to the final signature, every step was elegant, considered and beautifully managed.</p>
        <div class="who"><img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&q=80" alt=""><div><strong>Anya R.</strong><span>Aspen, CO</span></div></div>
      </div>
    </div>
  </div>
</section>

<section class="cta-strip">
  <div class="container" data-reveal>
    <span class="eyebrow">Private Client Service</span>
    <h2>Looking for something off-market?</h2>
    <p>Our advisors maintain confidential access to properties never listed publicly. A short conversation is all it takes.</p>
    <a href="contact.php" class="btn btn-gold">Request an Introduction</a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>