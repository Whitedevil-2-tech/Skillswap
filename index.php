<?php
require 'config.php';
$title = 'Browse Skills';
$q  = trim($_GET['q'] ?? '');
$me = (int)($_SESSION['user_id'] ?? 0);

// is_match = mutual match: their offer is my want AND their want is my offer
$sql = "SELECT l.*, u.username,
        EXISTS(SELECT 1 FROM listings m WHERE m.user_id = :me
               AND m.skill_want = l.skill_offer AND m.skill_offer = l.skill_want) AS is_match
        FROM listings l JOIN users u ON u.id = l.user_id
        WHERE (l.skill_offer LIKE :k1 OR l.skill_want LIKE :k2)
        ORDER BY is_match DESC, l.posted_date DESC";
$st = $pdo->prepare($sql);
$st->execute([':me' => $me, ':k1' => "%$q%", ':k2' => "%$q%"]);
$rows = $st->fetchAll();
require 'header.php';
?>
<section class="hero">
  <h1>Trade what you know for what you want to learn.</h1>
  <p>Teach Python, learn guitar. Offer design, get coding help. Zero money involved.</p>
  <form method="get" class="search">
    <input type="search" name="q" id="liveSearch" value="<?= e($q) ?>" placeholder="Search skills e.g. Photoshop, Java, Spanish…">
    <button class="btn">Search</button>
  </form>
</section>

<p class="muted"><?= count($rows) ?> listing(s) found</p>
<div class="grid">
<?php foreach ($rows as $r): ?>
  <article class="card" data-text="<?= e(strtolower($r['skill_offer'].' '.$r['skill_want'].' '.$r['username'])) ?>">
    <?php if ($r['is_match']): ?><span class="badge">★ Perfect match</span><?php endif; ?>
    <div class="swap">
      <div><small>OFFERS</small><strong class="offer"><?= e($r['skill_offer']) ?></strong></div>
      <span class="arrow">⇄</span>
      <div><small>WANTS</small><strong class="want"><?= e($r['skill_want']) ?></strong></div>
    </div>
    <p><?= nl2br(e($r['description'])) ?></p>
    <footer>
      <span class="user"><i class="avatar"><?= e(strtoupper($r['username'][0])) ?></i> <?= e($r['username']) ?>
        <small class="muted"> · <?= date('d M Y', strtotime($r['posted_date'])) ?></small></span>
      <?php if ($me && $me !== (int)$r['user_id']): ?>
        <form method="post" action="send_request.php">
          <input type="hidden" name="token" value="<?= csrf() ?>">
          <input type="hidden" name="listing_id" value="<?= (int)$r['id'] ?>">
          <button class="btn btn-sm">Request Swap</button>
        </form>
      <?php elseif (!$me): ?><a class="btn btn-sm" href="login.php">Login to swap</a>
      <?php else: ?><small class="muted">Your listing</small><?php endif; ?>
    </footer>
  </article>
<?php endforeach; ?>
</div>
<?php if (!$rows): ?><p class="empty">No skills found. Be the first to post one!</p><?php endif; ?>
<?php require 'footer.php'; ?>
