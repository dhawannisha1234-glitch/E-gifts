<?php
require_once __DIR__ . '/../includes/config.php';

if (is_admin()) {
    header('Location: index.php');
    exit;
}

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $user = find_user_by_email($email);
        if ($user && ($user['role'] ?? '') === 'admin' && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = 'admin';

            set_flash('success', 'Admin session started. Welcome, ' . htmlspecialchars($user['name']));
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid admin credentials or account does not have admin permissions.';
        }
    } else {
        $error = 'Please enter your admin credentials.';
    }
}

$pageTitle = 'Admin Portal Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login | <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body style="background-color: #0F172A; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">

    <div class="auth-card" style="box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div class="auth-header">
            <div class="brand-icon" style="margin: 0 auto 0.75rem; background: linear-gradient(135deg, #F59E0B, #D97706);">👑</div>
            <h2 class="auth-title">Admin Management Portal</h2>
            <p class="auth-subtitle">Restricted access for workshop managers and store administrators.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-box alert-error">
                <span>⚠️</span> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Demo Pill -->
        <div class="demo-login-box">
            <div style="font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: #64748B; margin-bottom: 0.4rem;">
                ⚡ 1-Click Admin Demo Login:
            </div>
            <button type="button" class="btn-demo-pill" style="width: 100%;" onclick="fillAdmin('admin@giftstudio.com', 'password123')">
                👑 Auto-Fill Admin Credentials
            </button>
        </div>

        <form action="login.php" method="POST" class="auth-form">
            <div class="form-group">
                <label class="form-label" for="adminEmail">Administrator Email</label>
                <input type="email" id="adminEmail" name="email" class="form-input" placeholder="admin@giftstudio.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="adminPass">Password</label>
                <input type="password" id="adminPass" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="save-cta-btn" style="padding: 0.85rem; margin-top: 1rem; background: linear-gradient(135deg, #0F172A, #334155);">
                Access Admin Dashboard &rarr;
            </button>
        </form>

        <div class="auth-footer">
            <a href="../index.php" style="color: #64748B; font-weight: 600; text-decoration: none;">&larr; Return to Customer Store</a>
        </div>
    </div>

<script>
function fillAdmin(email, pass) {
    document.getElementById('adminEmail').value = email;
    document.getElementById('adminPass').value = pass;
}
</script>
</body>
</html>
