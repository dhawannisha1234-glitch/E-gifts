<?php
require_once __DIR__ . '/includes/config.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $user = find_user_by_email($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] ?? 'customer';

            set_flash('success', 'Welcome back, ' . htmlspecialchars($user['name']) . '!');
            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    } else {
        $error = 'Please enter both email and password.';
    }
}

$pageTitle = 'Sign In to Your Account';
require_once __DIR__ . '/includes/header.php';
?>

<main class="section-container" style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-icon" style="margin: 0 auto 0.75rem;">🎁</div>
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">Sign in to access your saved customizations and track orders.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-box alert-error">
                <span>⚠️</span> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Quick 1-Click Demo Fill -->
        <div class="demo-login-box">
            <div style="font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: #64748B; margin-bottom: 0.4rem;">
                ⚡ 1-Click Demo Credentials:
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn-demo-pill" onclick="fillDemo('sarah@example.com', 'password123')">
                    👤 Customer Demo
                </button>
                <button type="button" class="btn-demo-pill" onclick="fillDemo('admin@giftstudio.com', 'password123')">
                    👑 Admin Demo
                </button>
            </div>
        </div>

        <form action="login.php" method="POST" class="auth-form">
            <div class="form-group">
                <label class="form-label" for="loginEmail">Email Address</label>
                <input type="email" id="loginEmail" name="email" class="form-input" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <label class="form-label" for="loginPass" style="margin-bottom: 0;">Password</label>
                    <a href="#" onclick="alert('Demo password is: password123')" style="font-size: 0.78rem; color: #4F46E5; text-decoration: none;">Forgot password?</a>
                </div>
                <input type="password" id="loginPass" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="save-cta-btn" style="padding: 0.85rem; margin-top: 1rem;">
                Sign In to Account
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account yet? <a href="register.php" style="color: #4F46E5; font-weight: 700; text-decoration: underline;">Sign Up Free</a>
        </div>
    </div>

</main>

<script>
function fillDemo(email, pass) {
    document.getElementById('loginEmail').value = email;
    document.getElementById('loginPass').value = pass;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
