<?php
require_once 'functions.php'; require_role('farmer'); verify_csrf();
$id=(int)($_GET['id']??0); $crop=null; $errors=[];
if ($id) { $s=db()->prepare("SELECT * FROM crops WHERE id=? AND farmer_id=?"); $s->execute([$id,current_user()['id']]); $crop=$s->fetch(); if(!$crop) { flash('Crop listing not found.','danger'); redirect('dashboard.php'); } }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??''); $description=trim($_POST['description']??'');
    $quantity=(float)($_POST['quantity']??0); $unit=$_POST['unit']??'kg'; $price=(float)($_POST['price']??0); $location=trim($_POST['location']??'');
    $status=$_POST['status']??'available';
    if($name==='') $errors[]='Crop name is required.';
    if($quantity<=0) $errors[]='Quantity must be greater than zero.';
    if($price<=0) $errors[]='Price must be greater than zero.';
    if($location==='') $errors[]='Location is required.';
    if(!in_array($unit,['kg','quintal','tonne','piece','dozen','bag'],true)) $errors[]='Choose a valid unit.';
    if(!in_array($status,['available','sold'],true)) $errors[]='Choose a valid status.';
    $image=$crop['image']??null;
    if(isset($_FILES['image']) && $_FILES['image']['error']!==UPLOAD_ERR_NO_FILE) {
        if($_FILES['image']['error']!==UPLOAD_ERR_OK) $errors[]='Image upload failed.';
        elseif($_FILES['image']['size']>3*1024*1024) $errors[]='Image must be smaller than 3 MB.';
        else {
            $info=getimagesize($_FILES['image']['tmp_name']);
            $mime=$info['mime']??'';
            $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;
            if(!$ext) $errors[]='Upload a JPG, PNG or WEBP image.';
            else { $new=bin2hex(random_bytes(12)).'.'.$ext; if(!is_dir(__DIR__.'/uploads')) mkdir(__DIR__.'/uploads',0755,true); if(move_uploaded_file($_FILES['image']['tmp_name'],__DIR__.'/uploads/'.$new)) { if($image && is_file(__DIR__.'/uploads/'.$image)) @unlink(__DIR__.'/uploads/'.$image); $image=$new; } else $errors[]='Could not save uploaded image.'; }
        }
    }
    if(!$errors) {
        if($id) { $s=db()->prepare("UPDATE crops SET name=?,description=?,quantity=?,unit=?,price=?,location=?,image=?,status=? WHERE id=? AND farmer_id=?"); $s->execute([$name,$description,$quantity,$unit,$price,$location,$image,$status,$id,current_user()['id']]); flash('Crop listing updated.'); }
        else { $s=db()->prepare("INSERT INTO crops (farmer_id,name,description,quantity,unit,price,location,image,status) VALUES (?,?,?,?,?,?,?,?,?)"); $s->execute([current_user()['id'],$name,$description,$quantity,$unit,$price,$location,$image,$status]); flash('Your crop is now listed on the marketplace.'); }
        redirect('dashboard.php');
    }
}
$val=fn($key,$default='')=>$_POST[$key]??($crop[$key]??$default);
header_html($id?'Edit crop':'Add a crop');
?>
<div class="form-page"><div class="page-intro"><span class="eyebrow">FARMER STUDIO</span><h1><?= $id?'Edit your listing':'List your harvest' ?></h1><p class="muted">Share accurate crop details so buyers know exactly what is available.</p></div><div class="panel form-panel"><?php foreach($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
<form method="post" enctype="multipart/form-data" class="form-stack" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Crop name<input name="name" required maxlength="120" value="<?= e($val('name')) ?>" placeholder="e.g. Organic tomatoes"></label>
<label>Description<textarea name="description" rows="4" maxlength="2000" placeholder="Describe quality, harvest date, variety and other useful details..."><?= e($val('description')) ?></textarea></label>
<div class="form-three"><label>Quantity<input type="number" name="quantity" min="0.01" step="0.01" required value="<?= e((string)$val('quantity')) ?>"></label><label>Unit<select name="unit"><?php foreach(['kg','quintal','tonne','piece','dozen','bag'] as $u): ?><option <?= $val('unit','kg')===$u?'selected':'' ?>><?= e($u) ?></option><?php endforeach; ?></select></label><label>Price per unit (₹)<input type="number" name="price" min="0.01" step="0.01" required value="<?= e((string)$val('price')) ?>"></label></div>
<label>Farm location<input name="location" required value="<?= e($val('location',current_user()['location']??'')) ?>" placeholder="Village, district, state"></label>
<label>Crop photo <small class="muted">(JPG, PNG or WEBP · max 3 MB)</small><input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
<?php if($crop && $crop['image']): ?><img class="form-preview" src="<?= e(crop_image($crop['image'])) ?>" alt="Current crop image"><?php endif; ?>
<?php if($id): ?><label>Listing status<select name="status"><option value="available" <?= $val('status')==='available'?'selected':'' ?>>Available</option><option value="sold" <?= $val('status')==='sold'?'selected':'' ?>>Sold</option></select></label><?php endif; ?>
<div class="form-actions"><a class="btn btn-ghost" href="dashboard.php">Cancel</a><button class="btn"><?= $id?'Save changes':'Publish crop listing' ?></button></div></form></div></div>
<?php footer_html(); ?>
