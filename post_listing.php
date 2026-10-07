<?php
require 'config.php';
require_login();
$title = 'Post a Skill'; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $o = trim($_POST['skill_offer'] ?? ''); $w = trim($_POST['skill_want'] ?? ''); $d = trim($_POST['description'] ?? '');
    if ($o === '' || $w === '') $errors[] = 'Both skills are required.';
    if (mb_strlen($o) > 100 || mb_strlen($w) > 100) $errors[] = 'Skill names max 100 characters.';
    if (mb_strlen($d) > 1000) $errors[] = 'Description max 1000 characters.';
    if (!$errors) {
        $pdo->prepare('INSERT INTO listings (user_id,skill_offer,skill_want,description) VALUES (?,?,?,?)')
            ->execute([$_SESSION['user_id'], $o, $w, $d]);
        flash('Listing posted!');
        header('Location: dashboard.php'); exit;
    }
}
require 'header.php';
?>
<div class="auth">
  <h2>Post a skill swap</h2>
  <?php foreach ($errors as $er): ?><div class="flash err"><?= e($er) ?></div><?php endforeach; ?>
  <form method="post" class="form" novalidate>
    <input type="hidden" name="token" value="<?= csrf() ?>">
    <label>I can offer<input name="skill_offer" maxlength="100" required placeholder="e.g. Python Programming"></label>
    <label>I want to learn<input name="skill_want" maxlength="100" required placeholder="e.g. Graphic Design"></label>
    <label>Details<textarea name="description" rows="4" maxlength="1000" placeholder="Your level, availability, preferred swap terms…"></textarea></label>
    <button class="btn block">Publish listing</button>
  </form>
</div>
<?php require 'footer.php'; ?>
