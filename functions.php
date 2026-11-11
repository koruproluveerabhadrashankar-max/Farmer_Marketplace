<?php
require_once __DIR__ . '/config.php';

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}
function is_logged_in(): bool {
    return isset($_SESSION['user']['id']);
}
function current_user(): array {
    return $_SESSION['user'] ?? [];
}
function require_login(): void {
    if (!is_logged_in()) {
        flash('Please log in to continue.', 'warning');
        redirect('login.php');
    }
}
function require_role(string $role): void {
    require_login();
    if ((current_user()['role'] ?? '') !== $role) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }
}
function flash(string $message, string $type='success'): void {
    $_SESSION['flash'] = ['message'=>$message, 'type'=>$type];
}
function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert alert-' . e($f['type']) . '">' . e($f['message']) . '</div>';
        unset($_SESSION['flash']);
    }
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sent = $_POST['csrf_token'] ?? '';
        if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
            http_response_code(419);
            exit('Invalid form token. Go back and try again.');
        }
    }
}
function money(float|string $amount): string {
    return '₹' . number_format((float)$amount, 2);
}
function crop_image(?string $image): string {
    if ($image && preg_match('/^[a-zA-Z0-9._-]+$/', $image)) return 'uploads/' . $image;
    return 'assets/images/crop-placeholder.svg';
}
function header_html(string $title='Farmer Marketplace'): void { ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | FarmDirect</title>
<link rel="stylesheet" href="assets/css/style.css">
<script defer src="assets/js/app.js"></script>
</head><body>
<header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">🌱</span> FarmDirect</a>
<nav>
<a href="index.php">Marketplace</a>
<?php if (is_logged_in()): ?>
<a href="dashboard.php">Dashboard</a>
<?php if ((current_user()['role'] ?? '') === 'admin'): ?><a href="admin.php">Admin</a><?php endif; ?>
<span class="nav-user">Hi, <?= e(current_user()['name'] ?? '') ?></span>
<a class="nav-button" href="logout.php">Logout</a>
<?php else: ?><a href="login.php">Login</a><a class="nav-button" href="register.php">Join free</a><?php endif; ?>
</nav></header><main class="container">
<?php show_flash(); ?>
<?php }
function footer_html(): void { ?>
</main><footer class="footer"><div>🌱 <strong>FarmDirect</strong> — Connecting farms to families.</div><small>Mini Project • PHP · MySQL · JavaScript</small></footer></body></html>
<?php }
