<?php $uid = $_SESSION['user_id'] ?? null; $f = flash(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Home') ?> · SkillSwap</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="nav">
  <a class="logo" href="index.php">⇄ Skill<span>Swap</span></a>
  <nav>
    <a href="index.php">Browse</a>
    <?php if ($uid): ?>
      <a href="post_listing.php">Post Skill</a>
      <a href="dashboard.php">Dashboard</a>
      <a class="btn btn-ghost" href="logout.php">Logout (<?= e($_SESSION['username']) ?>)</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a class="btn" href="register.php">Join free</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
<?php if ($f): ?><div class="flash <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
