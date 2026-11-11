<?php
require_once 'functions.php'; require_role('admin'); verify_csrf();
if($_SERVER['REQUEST_METHOD']==='POST') {
    $id=(int)($_POST['id']??0); $action=$_POST['action']??'';
    if($action==='delete') { $s=db()->prepare("DELETE FROM crops WHERE id=?"); $s->execute([$id]); flash('Crop listing removed.'); }
    elseif($action==='toggle') { $s=db()->prepare("UPDATE crops SET status=IF(status='available','sold','available') WHERE id=?"); $s->execute([$id]); flash('Crop status updated.'); }
    redirect('admin.php');
}
$users=db()->query("SELECT id,name,email,role,location,created_at FROM users ORDER BY created_at DESC")->fetchAll();
$crops=db()->query("SELECT c.*,u.name AS farmer_name FROM crops c JOIN users u ON u.id=c.farmer_id ORDER BY c.created_at DESC")->fetchAll();
$stats=['users'=>count($users),'farmers'=>count(array_filter($users,fn($u)=>$u['role']==='farmer')),'crops'=>count($crops),'available'=>count(array_filter($crops,fn($c)=>$c['status']==='available'))];
header_html('Admin dashboard');
?>
<section class="dashboard-heading"><div><span class="eyebrow">MARKETPLACE CONTROL</span><h1>Admin dashboard</h1><p class="muted">A quick overview of the FarmDirect community.</p></div></section>
<div class="stat-grid four"><div class="stat-card"><span>Total users</span><b><?= $stats['users'] ?></b></div><div class="stat-card"><span>Farmers</span><b><?= $stats['farmers'] ?></b></div><div class="stat-card"><span>All listings</span><b><?= $stats['crops'] ?></b></div><div class="stat-card"><span>Available</span><b><?= $stats['available'] ?></b></div></div>
<div class="section-head"><div><h2>Crop listings</h2><p>Review listings and update their status.</p></div></div><div class="table-wrap"><table><thead><tr><th>Crop</th><th>Farmer</th><th>Price</th><th>Location</th><th>Status</th><th>Manage</th></tr></thead><tbody><?php foreach($crops as $c): ?><tr><td><b><?= e($c['name']) ?></b></td><td><?= e($c['farmer_name']) ?></td><td><?= money($c['price']) ?>/<?= e($c['unit']) ?></td><td><?= e($c['location']) ?></td><td><span class="status status-<?= e($c['status']) ?>"><?= ucfirst($c['status']) ?></span></td><td><div class="action-links"><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="action" value="toggle"><button class="link-button"><?= $c['status']==='available'?'Mark sold':'Make available' ?></button></form><form method="post" data-confirm="Permanently delete this listing?"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="action" value="delete"><button class="link-danger">Delete</button></form></div></td></tr><?php endforeach; ?><?php if(!$crops): ?><tr><td colspan="6" class="table-empty">No crop listings yet.</td></tr><?php endif; ?></tbody></table></div>
<div class="section-head"><div><h2>Registered users</h2><p>Farmer and buyer accounts on the platform.</p></div></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Location</th><th>Joined</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><span class="tag"><?= ucfirst($u['role']) ?></span></td><td><?= e($u['location']) ?></td><td><?= e(date('d M Y',strtotime($u['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php footer_html(); ?>
