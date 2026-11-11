<?php
require_once 'functions.php'; require_login();
$user=current_user();
if ($user['role']==='admin') redirect('admin.php');
if ($user['role']==='farmer') {
    $stmt=db()->prepare("SELECT * FROM crops WHERE farmer_id=? ORDER BY created_at DESC"); $stmt->execute([$user['id']]); $crops=$stmt->fetchAll();
    $total=count($crops); $available=count(array_filter($crops,fn($c)=>$c['status']==='available')); $sold=$total-$available;
} else {
    $total=(int)db()->query("SELECT COUNT(*) FROM crops WHERE status='available'")->fetchColumn();
}
header_html('My dashboard');
?>
<section class="dashboard-heading"><div><span class="eyebrow">YOUR FARM DIRECT SPACE</span><h1>Hello, <?= e($user['name']) ?> 👋</h1><p class="muted"><?= $user['role']==='farmer'?'Manage your crop listings and keep buyers updated.':'Discover fresh produce directly from local farmers.' ?></p></div><?php if($user['role']==='farmer'): ?><a class="btn" href="crop_form.php">＋ Add a crop</a><?php else: ?><a class="btn" href="index.php#market">Explore marketplace</a><?php endif; ?></section>
<?php if($user['role']==='farmer'): ?>
<div class="stat-grid"><div class="stat-card"><span>Total listings</span><b><?= $total ?></b><small>All your crops</small></div><div class="stat-card"><span>Available</span><b><?= $available ?></b><small>Visible to buyers</small></div><div class="stat-card"><span>Marked sold</span><b><?= $sold ?></b><small>Completed listings</small></div></div>
<div class="section-head"><div><h2>Your crop listings</h2><p>Update availability or edit crop information.</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Crop</th><th>Quantity</th><th>Price / unit</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($crops as $c): ?><tr><td><div class="table-crop"><img src="<?= e(crop_image($c['image'])) ?>" alt=""><b><?= e($c['name']) ?></b></div></td><td><?= e($c['quantity']) ?> <?= e($c['unit']) ?></td><td><?= money($c['price']) ?></td><td><?= e($c['location']) ?></td><td><span class="status status-<?= e($c['status']) ?>"><?= ucfirst($c['status']) ?></span></td><td><div class="action-links"><a href="crop_form.php?id=<?= (int)$c['id'] ?>">Edit</a><a href="crop.php?id=<?= (int)$c['id'] ?>">View</a><form method="post" action="delete_crop.php" data-confirm="Delete this crop listing?"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="link-danger">Delete</button></form></div></td></tr><?php endforeach; ?>
<?php if(!$crops): ?><tr><td colspan="6" class="table-empty">You have not added any crops yet. <a href="crop_form.php">Add your first crop</a>.</td></tr><?php endif; ?></tbody></table></div>
<?php else: ?><div class="buyer-welcome"><div class="big-icon">🧺</div><div><h2>Ready to find fresh produce?</h2><p>There are currently <b><?= $total ?></b> crop listings available. Search by crop, compare prices and contact farmers directly.</p><a class="text-link" href="index.php#market">Browse the marketplace →</a></div></div><?php endif; ?>
<?php footer_html(); ?>
