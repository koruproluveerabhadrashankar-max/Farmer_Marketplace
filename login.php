<?php
require_once 'functions.php';
if (is_logged_in()) redirect('dashboard.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
    $stmt=db()->prepare("SELECT id,name,email,role,location,password FROM users WHERE email=? LIMIT 1"); $stmt->execute([$email]); $user=$stmt->fetch();
    if ($user && password_verify($password,$user['password'])) {
        session_regenerate_id(true);
        unset($user['password']); $_SESSION['user']=$user;
        flash('Welcome back, '.$user['name'].'!');
        redirect('dashboard.php');
    } else $error='Email or password is incorrect.';
}
header_html('Login');
?>
<div class="auth-wrap login-wrap"><div class="auth-aside"><span class="eyebrow">WELCOME BACK</span><h1>Your farm-to-table<br><em>journey continues.</em></h1><p>Log in to manage your listings, discover fresh crops and connect directly.</p><div class="auth-illustration">🌻 🧺 🍅</div></div><div class="auth-card"><h2>Welcome back</h2><p class="muted">Enter your details to sign in.</p><?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="form-stack"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Email address<input type="email" name="email" required value="<?= e($_POST['email']??'') ?>" placeholder="you@example.com"></label><label>Password<input type="password" name="password" required placeholder="Your password"></label><button class="btn btn-full">Log in</button></form><p class="auth-foot">New to FarmDirect? <a href="register.php">Create an account</a></p></div></div>
<?php footer_html(); ?>
