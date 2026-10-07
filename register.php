<?php
require 'config.php';
$title = 'Register'; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $u = trim($_POST['username'] ?? ''); $m = trim($_POST['email'] ?? '');
    $p = $_POST['password'] ?? ''; $c = $_POST['confirm'] ?? '';
    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $u)) $errors[] = 'Username: 3-50 letters, numbers or underscores.';
    if (!filter_var($m, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if (strlen($p) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($p !== $c) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        $chk = $pdo->prepare('SELECT id FROM users WHERE username=? OR email=?');
        $chk->execute([$u, $m]);
        if ($chk->fetch()) $errors[] = 'Username or email already registered.';
    }
    if (!$errors) {
        $pdo->prepare('INSERT INTO users (username,email,password) VALUES (?,?,?)')
            ->execute([$u, $m, password_hash($p, PASSWORD_DEFAULT)]);
        flash('Account created! Please log in.');
        header('Location: login.php'); exit;
    }
}
require 'header.php';
?>
<div class="auth">
  <h2>Create your account</h2>
  <?php foreach ($errors as $er): ?><div class="flash err"><?= e($er) ?></div><?php endforeach; ?>
  <form method="post" class="form" id="regForm" novalidate>
    <input type="hidden" name="token" value="<?= csrf() ?>">
    <label>Username<input name="username" required value="<?= e($_POST['username'] ?? '') ?>"></label>
    <label>Email<input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
    <label>Password<input type="password" name="password" id="pw" required minlength="8"></label>
    <div class="meter"><i id="meterBar"></i></div>
    <label>Confirm password<input type="password" name="confirm" id="pw2" required></label>
    <label class="inline"><input type="checkbox" id="showPw"> Show passwords</label>
    <button class="btn block">Sign up</button>
    <p class="muted">Already a member? <a href="login.php">Login</a></p>
  </form>
</div>
<?php require 'footer.php'; ?>
