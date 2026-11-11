<?php
require_once 'functions.php';
$id=(int)($_GET['id']??0);
$stmt=db()->prepare("SELECT c.*,u.name AS farmer_name,u.phone AS farmer_phone,u.email AS farmer_email,u.location AS farmer_location FROM crops c JOIN users u ON u.id=c.farmer_id WHERE c.id=?"); $stmt->execute([$id]); $crop=$stmt->fetch();
if(!$crop) { http_response_code(404); header_html('Crop not found'); echo '<div class="empty-state"><h2>Crop not found</h2><a class="btn" href="index.php">Back to marketplace</a></div>'; footer_html(); exit; }
$own=is_logged_in() && (int)current_user()['id']===(int)$crop['farmer_id'];
header_html($crop['name']);
?>
<div class="detail-layout"><div class="detail-image"><img src="<?= e(crop_image($crop['image'])) ?>" alt="<?= e($crop['name']) ?>"></div><div class="detail-info"><a class="back-link" href="index.php">← Back to marketplace</a><div class="detail-tags"><span class="tag <?= $crop['status']==='sold'?'tag-sold':'' ?>"><?= ucfirst($crop['status']) ?></span><span>📍 <?= e($crop['location']) ?></span></div><h1><?= e($crop['name']) ?></h1><div class="detail-price"><?= money($crop['price']) ?><small> / <?= e($crop['unit']) ?></small></div><p class="quantity-line"><?= e($crop['quantity']) ?> <?= e($crop['unit']) ?> listed</p><p class="detail-description"><?= nl2br(e($crop['description']?:'The farmer has not added a description yet.')) ?></p><div class="seller-card"><div class="seller-avatar">👨‍🌾</div><div><small>SELLER / FARMER</small><b><?= e($crop['farmer_name']) ?></b><span><?= e($crop['farmer_location']?:$crop['location']) ?></span></div></div>
<?php if($crop['status']==='available'): ?><div class="contact-actions"><?php if($crop['farmer_phone']): ?><a class="btn" href="tel:<?= e(preg_replace('/[^0-9+]/','',$crop['farmer_phone'])) ?>">☎ Call farmer</a><?php endif; ?><a class="btn btn-outline" href="mailto:<?= e($crop['farmer_email']) ?>?subject=Enquiry%20about%20<?= rawurlencode($crop['name']) ?>">✉ Email farmer</a></div><?php else: ?><div class="alert alert-warning">This crop has been marked as sold.</div><?php endif; ?>
<?php if($own): ?><a class="text-link" href="crop_form.php?id=<?= (int)$crop['id'] ?>">Edit your listing →</a><?php endif; ?></div></div>
<?php footer_html(); ?>
