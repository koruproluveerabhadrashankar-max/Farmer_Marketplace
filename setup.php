<?php
require_once __DIR__ . '/config.php';
$message = '';
$error = '';
try {
    $server = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $server->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $server->exec("USE `" . DB_NAME . "`");
    $server->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        phone VARCHAR(25) DEFAULT NULL,
        location VARCHAR(150) DEFAULT NULL,
        role ENUM('farmer','buyer','admin') NOT NULL DEFAULT 'buyer',
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    $server->exec("CREATE TABLE IF NOT EXISTS crops (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        farmer_id INT UNSIGNED NOT NULL,
        name VARCHAR(120) NOT NULL,
        description TEXT,
        quantity DECIMAL(10,2) NOT NULL,
        unit VARCHAR(20) NOT NULL DEFAULT 'kg',
        price DECIMAL(10,2) NOT NULL,
        location VARCHAR(150) NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        status ENUM('available','sold') NOT NULL DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_crop_farmer FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_crop_name (name), INDEX idx_crop_location (location), INDEX idx_crop_status (status)
    ) ENGINE=InnoDB");
    $check = $server->prepare("SELECT id FROM users WHERE email=?");
    $check->execute(['admin@farmdirect.local']);
    if (!$check->fetch()) {
        $stmt = $server->prepare("INSERT INTO users (name,email,phone,location,role,password) VALUES (?,?,?,?,?,?)");
        $stmt->execute(['FarmDirect Admin','admin@farmdirect.local','9999999999','Admin Office','admin',password_hash('Admin@123', PASSWORD_DEFAULT)]);
    }
    $message = 'Database and tables created successfully. The demo administrator account is ready.';
} catch (Throwable $ex) {
    $error = $ex->getMessage();
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>FarmDirect Setup</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body><main class="container setup-card"><div class="brand"><span class="brand-mark">🌱</span> FarmDirect setup</div>
<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<h2>Setup complete!</h2><p>Now open the marketplace and sign in with the demo administrator details below.</p>
<div class="credential"><b>Email</b><span>admin@farmdirect.local</span><b>Password</b><span>Admin@123</span></div>
<p class="notice">For safety, delete <code>setup.php</code> after setup. Change the demo admin password before real use.</p><a class="btn" href="index.php">Open FarmDirect</a>
<?php else: ?><div class="alert alert-danger">Setup failed: <?= htmlspecialchars($error) ?></div><p>Check that Apache and MySQL are running in XAMPP and the database settings in config.php are correct.</p><a href="setup.php">Try again</a><?php endif; ?>
</main></body></html>
