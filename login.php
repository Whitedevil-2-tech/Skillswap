<?php
require 'config.php';
$title = 'Login'; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $st = $pdo->prepare('SELECT * FROM users WHERE email=?');
    $st->execute([trim($_POST['email'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['username'] = $u['username'];
        header('Location: dashboard.php'); exit;
    }
    $error = 'Invalid email or password.';
}
require 'header.php';
?>
<div class="auth">
  <h2>Welcome back</h2>
  <?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="form" novalidate>
    <input type="hidden" name="token" value="<?= csrf() ?>">
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" id="pw" required></label>
    <label class="inline"><input type="checkbox" id="showPw"> Show password</label>
    <button class="btn block">Login</button>
    <p class="muted">New here? <a href="register.php">Create an account</a></p>
  </form>
</div>
<?php require 'footer.php'; ?>
