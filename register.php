<?php
require_once 'functions.php';
if (is_logged_in()) redirect('dashboard.php');
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??''));
    $phone=trim($_POST['phone']??''); $location=trim($_POST['location']??'');
    $role=$_POST['role']??'buyer'; $password=$_POST['password']??''; $confirm=$_POST['confirm_password']??'';
    if (mb_strlen($name)<2) $errors[]='Enter your full name.';
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Enter a valid email address.';
    if (!in_array($role,['farmer','buyer'],true)) $errors[]='Choose a valid account type.';
    if (strlen($password)<8) $errors[]='Password must contain at least 8 characters.';
    if ($password!==$confirm) $errors[]='Passwords do not match.';
    if (!$errors) {
        $check=db()->prepare("SELECT id FROM users WHERE email=?"); $check->execute([$email]);
        if ($check->fetch()) $errors[]='An account with this email already exists.';
        else {
            $stmt=db()->prepare("INSERT INTO users (name,email,phone,location,role,password) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$name,$email,$phone,$location,$role,password_hash($password,PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user']=['id'=>(int)db()->lastInsertId(),'name'=>$name,'email'=>$email,'role'=>$role,'location'=>$location];
            flash('Welcome to FarmDirect! Your account is ready.');
            redirect('dashboard.php');
        }
    }
}
header_html('Create account');
?>
<div class="auth-wrap"><div class="auth-aside"><span class="eyebrow">JOIN THE COMMUNITY</span><h1>Better harvests.<br><em>Better connections.</em></h1><p>Create your free account to buy fresh crops or sell directly to customers.</p><div class="auth-illustration">👩‍🌾 🌾 🥬</div></div><div class="auth-card"><h2>Create account</h2><p class="muted">It only takes a minute to get started.</p><?php foreach($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
<form method="post" class="form-stack" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Full name<input name="name" required maxlength="100" value="<?= e($_POST['name']??'') ?>" placeholder="Your name"></label>
<label>Email address<input type="email" name="email" required value="<?= e($_POST['email']??'') ?>" placeholder="you@example.com"></label>
<div class="form-two"><label>Phone number<input name="phone" value="<?= e($_POST['phone']??'') ?>" placeholder="Mobile number"></label><label>Location<input name="location" value="<?= e($_POST['location']??'') ?>" placeholder="Your town / village"></label></div>
<label>Account type<select name="role"><option value="buyer" <?= (($_POST['role']??$_GET['role']??'buyer')==='buyer'?'selected':'') ?>>Buyer</option><option value="farmer" <?= (($_POST['role']??$_GET['role']??'')==='farmer'?'selected':'') ?>>Farmer / Seller</option></select></label>
<label>Password<input type="password" name="password" minlength="8" required placeholder="At least 8 characters"></label><label>Confirm password<input type="password" name="confirm_password" required placeholder="Enter password again"></label>
<button class="btn btn-full">Create my account</button></form><p class="auth-foot">Already registered? <a href="login.php">Log in</a></p></div></div>
<?php footer_html(); ?>
