<?php require_once('header.php'); ?>

<?php
ensureAchieverTable($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT * FROM tbl_achiever WHERE id=?");
$statement->execute(array($id));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
if (!$result) {
    header('location: logout.php');
    exit;
}

$photo = $result[0]['photo'];
$statement = $pdo->prepare("DELETE FROM tbl_achiever WHERE id=?");
$statement->execute(array($id));
adminDeleteUploadIfUnused($pdo, $photo, 'tbl_achiever', 'photo');

header('location: achiever.php?deleted=1');
exit;
