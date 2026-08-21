<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT * FROM tbl_client WHERE id=?");
$statement->execute(array($id));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
if (!$result) {
    header('location: logout.php');
    exit;
}

$logo = $result[0]['logo'];
$statement = $pdo->prepare("DELETE FROM tbl_client WHERE id=?");
$statement->execute(array($id));
adminDeleteUploadIfUnused($pdo, $logo, 'tbl_client', 'logo');

header('location: client.php?deleted=1');
exit;
