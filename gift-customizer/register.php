<?php
require_once __DIR__ . '/includes/config.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (find_user_by_email($email)) {
        $error = 'An account with this email address already exists. Please log in.';
    } else {
        $newUser = create_user($name, $email, $password, $phone, $address);
        $_SESSION['user_id'] = $newUser['id'];
        $_SESSION['user_name'] = $newUser['name'];
        $_SESSION['user_email'] = $newUser['email'];
        $_SESSION['user_role'] = 'customer';

        set_flash('success', 'Your account has been created! Welcome to CraftGift Studio.');
        header('Location: dashboard.php');
        exit;
    }
}

$pageTitle = 'Create a New Account — CraftGift Studio';
require_once __DIR__ . '/includes/header.php';
?>

<main class="section-container" style="display: flex; justify-content: center; align-items: center; min-height: 75vh; padding: 2rem 1rem;">
    
    <div class="auth-card" style="max-width: 480px;">
        <div class="auth-header">
            <div class="brand-icon" style="margin: 0 auto 0.75rem;">✨</div>
            <h2 class="auth-title">Create an Account</h2>
            <p class="auth-subtitle">Save your personalized gift designs, track orders, and get member discounts.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-box alert-error">
                <span>⚠️</span> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="auth-form">
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-input" placeholder="e.g. Sarah Jenkins" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-input" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">Password (min 6 chars) *</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-input" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number (Optional)</label>
                <input type="tel" name="phone" class="form-input" placeholder="+1 (555) 000-0000" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Default Shipping Address (Optional)</label>
                <textarea name="address" rows="2" class="form-input" placeholder="Street, City, State, ZIP"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="save-cta-btn" style="padding: 0.85rem; margin-top: 0.5rem;">
                Create Free Account 🎁
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php" style="color: #4F46E5; font-weight: 700; text-decoration: underline;">Log In Here</a>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
