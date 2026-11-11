<?php
require_once 'functions.php';
$q = trim($_GET['q'] ?? '');
$location = trim($_GET['location'] ?? '');
$max_price = trim($_GET['max_price'] ?? '');
$sql = "SELECT c.*, u.name AS farmer_name, u.phone AS farmer_phone FROM crops c JOIN users u ON u.id=c.farmer_id WHERE c.status='available'";
$params = [];
if ($q !== '') { $sql .= " AND (c.name LIKE ? OR c.description LIKE ?)"; $params[]="%$q%"; $params[]="%$q%"; }
if ($location !== '') { $sql .= " AND c.location LIKE ?"; $params[]="%$location%"; }
if ($max_price !== '' && is_numeric($max_price)) { $sql .= " AND c.price <= ?"; $params[]=(float)$max_price; }
$sql .= " ORDER BY c.created_at DESC";
$stmt = db()->prepare($sql); $stmt->execute($params); $crops = $stmt->fetchAll();
header_html('Fresh crops marketplace');
?>
<section class="hero"><div class="hero-copy"><span class="eyebrow">FRESH FROM THE FARM</span><h1>Good food starts<br>with <em>good connections.</em></h1><p>Discover fresh crops directly from local farmers. Fair prices, clear details, no unnecessary middlemen.</p><div class="hero-actions"><a class="btn" href="#market">Explore crops</a><a class="btn btn-outline-light" href="register.php">Sell your harvest</a></div><div class="hero-stats"><div><b>Direct</b><small>farmer connection</small></div><div><b>Fair</b><small>transparent pricing</small></div><div><b>Fresh</b><small>local produce</small></div></div></div><div class="hero-art"><div class="sun"></div><div class="farm-emoji">🌾</div><div class="floating-note">🌿 Fresh harvest<br><small>Closer to your table</small></div></div></section>
<section id="market" class="section-head"><div><span class="eyebrow">THE MARKETPLACE</span><h2>Find your fresh picks</h2><p>Search crops and connect with the farmer directly.</p></div><span class="count-pill"><?= count($crops) ?> available listings</span></section>
<form class="search-panel" method="get"><label>Crop name<input name="q" value="<?= e($q) ?>" placeholder="e.g. Tomato, Rice, Onion"></label><label>Location<input name="location" value="<?= e($location) ?>" placeholder="Village, town or district"></label><label>Max price / unit<input name="max_price" type="number" min="0" step="0.01" value="<?= e($max_price) ?>" placeholder="Any price"></label><button class="btn" type="submit">Search crops</button></form>
<div class="crop-grid">
<?php foreach ($crops as $crop): ?><article class="crop-card"><a class="crop-photo" href="crop.php?id=<?= (int)$crop['id'] ?>"><img src="<?= e(crop_image($crop['image'])) ?>" alt="<?= e($crop['name']) ?>"></a><div class="crop-content"><div class="crop-meta"><span class="tag">Available</span><span>📍 <?= e($crop['location']) ?></span></div><h3><a href="crop.php?id=<?= (int)$crop['id'] ?>"><?= e($crop['name']) ?></a></h3><p class="muted"><?= e(mb_strimwidth((string)$crop['description'],0,95,'…')) ?></p><div class="price-row"><div><strong><?= money($crop['price']) ?></strong><small>per <?= e($crop['unit']) ?></small></div><span><?= e($crop['quantity']) ?> <?= e($crop['unit']) ?> available</span></div><a class="text-link" href="crop.php?id=<?= (int)$crop['id'] ?>">View crop details →</a></div></article><?php endforeach; ?>
<?php if (!$crops): ?><div class="empty-state"><div>🌱</div><h3>No crops found</h3><p>Try a different crop name or location, or check back later.</p><a class="btn" href="index.php">Clear filters</a></div><?php endif; ?>
</div>
<section class="join-banner"><div><span class="eyebrow">GROW WITH US</span><h2>Are you a farmer?</h2><p>Share your harvest with more buyers and get discovered.</p></div><a class="btn btn-light" href="register.php?role=farmer">Start selling free →</a></section>
<?php footer_html(); ?>
