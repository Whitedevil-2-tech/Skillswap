<?php
require 'config.php';
require_login();
$title = 'Dashboard'; $me = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare('DELETE FROM listings WHERE id=? AND user_id=?')->execute([$id, $me]);
        flash('Listing deleted.');
    } elseif (in_array($_POST['action'] ?? '', ['Accepted', 'Rejected'], true)) {
        $pdo->prepare("UPDATE swap_requests SET status=? WHERE id=? AND receiver_id=? AND status='Pending'")
            ->execute([$_POST['action'], $id, $me]);
        flash('Request ' . strtolower($_POST['action']) . '.');
    }
    header('Location: dashboard.php'); exit;
}

$mine = $pdo->prepare('SELECT * FROM listings WHERE user_id=? ORDER BY posted_date DESC');
$mine->execute([$me]); $mine = $mine->fetchAll();

$in = $pdo->prepare('SELECT r.*, u.username, u.email FROM swap_requests r JOIN users u ON u.id=r.sender_id WHERE r.receiver_id=? ORDER BY r.request_date DESC');
$in->execute([$me]); $in = $in->fetchAll();

$out = $pdo->prepare('SELECT r.*, u.username, u.email FROM swap_requests r JOIN users u ON u.id=r.receiver_id WHERE r.sender_id=? ORDER BY r.request_date DESC');
$out->execute([$me]); $out = $out->fetchAll();

$pending = count(array_filter($in, fn($r) => $r['status'] === 'Pending'));
require 'header.php';
?>
<h2>Hi, <?= e($_SESSION['username']) ?> 👋</h2>
<div class="stats">
  <div><b><?= count($mine) ?></b>Active listings</div>
  <div><b><?= $pending ?></b>Pending to review</div>
  <div><b><?= count(array_filter(array_merge($in, $out), fn($r) => $r['status'] === 'Accepted')) ?></b>Swaps accepted</div>
</div>

<div class="panels">
<section class="panel">
  <h3>My Active Listings <a class="btn btn-sm" href="post_listing.php">+ New</a></h3>
  <?php foreach ($mine as $l): ?>
    <div class="row">
      <div><strong class="offer"><?= e($l['skill_offer']) ?></strong> ⇄ <strong class="want"><?= e($l['skill_want']) ?></strong></div>
      <form method="post" onsubmit="return confirm('Delete this listing?')">
        <input type="hidden" name="token" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
        <button name="action" value="delete" class="btn btn-sm btn-danger">Delete</button>
      </form>
    </div>
  <?php endforeach; if (!$mine) echo '<p class="empty">No listings yet.</p>'; ?>
</section>

<section class="panel">
  <h3>Incoming Requests</h3>
  <?php foreach ($in as $r): ?>
    <div class="row">
      <div><strong><?= e($r['username']) ?></strong> wants to swap<br>
        <small class="muted"><?= date('d M Y', strtotime($r['request_date'])) ?></small>
        <?php if ($r['status'] === 'Accepted'): ?><br><small>Contact: <a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a></small><?php endif; ?></div>
      <?php if ($r['status'] === 'Pending'): ?>
      <form method="post" class="actions">
        <input type="hidden" name="token" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <button name="action" value="Accepted" class="btn btn-sm">Accept</button>
        <button name="action" value="Rejected" class="btn btn-sm btn-danger">Reject</button>
      </form>
      <?php else: ?><span class="pill <?= e($r['status']) ?>"><?= e($r['status']) ?></span><?php endif; ?>
    </div>
  <?php endforeach; if (!$in) echo '<p class="empty">No incoming requests.</p>'; ?>
</section>

<section class="panel">
  <h3>Outgoing Requests</h3>
  <?php foreach ($out as $r): ?>
    <div class="row">
      <div>To <strong><?= e($r['username']) ?></strong><br><small class="muted"><?= date('d M Y', strtotime($r['request_date'])) ?></small>
        <?php if ($r['status'] === 'Accepted'): ?><br><small>Contact: <a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a></small><?php endif; ?></div>
      <span class="pill <?= e($r['status']) ?>"><?= e($r['status']) ?></span>
    </div>
  <?php endforeach; if (!$out) echo '<p class="empty">No outgoing requests.</p>'; ?>
</section>
</div>
<?php require 'footer.php'; ?>
