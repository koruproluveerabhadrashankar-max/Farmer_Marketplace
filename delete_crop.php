<?php
require_once 'functions.php'; require_role('farmer'); verify_csrf();
$id=(int)($_POST['id']??0);
$stmt=db()->prepare("DELETE FROM crops WHERE id=? AND farmer_id=?");
$stmt->execute([$id,current_user()['id']]);
flash($stmt->rowCount()?'Crop listing deleted.':'Crop listing not found.',$stmt->rowCount()?'success':'warning');
redirect('dashboard.php');
