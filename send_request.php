<?php
require 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
check_csrf();

$me = (int)$_SESSION['user_id'];
$st = $pdo->prepare('SELECT user_id FROM listings WHERE id=?');
$st->execute([(int)($_POST['listing_id'] ?? 0)]);
$owner = $st->fetchColumn();

if (!$owner)               flash('Listing not found.', 'err');
elseif ((int)$owner === $me) flash('You cannot request your own listing.', 'err');
else {
    $dup = $pdo->prepare("SELECT id FROM swap_requests WHERE sender_id=? AND receiver_id=? AND status='Pending'");
    $dup->execute([$me, $owner]);
    if ($dup->fetch()) flash('You already have a pending request with this user.', 'err');
    else {
        $pdo->prepare('INSERT INTO swap_requests (sender_id,receiver_id) VALUES (?,?)')->execute([$me, $owner]);
        flash('Swap request sent!');
    }
}
header('Location: dashboard.php');
