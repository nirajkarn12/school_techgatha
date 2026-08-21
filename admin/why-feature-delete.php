<?php require_once('header.php'); ?>

<?php
ensureWhyFeatureTable($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT * FROM tbl_why_feature WHERE id=?");
$statement->execute(array($id));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
if (!$result) {
    header('location: logout.php');
    exit;
}

$icon = $result[0]['icon'];
$statement = $pdo->prepare("DELETE FROM tbl_why_feature WHERE id=?");
$statement->execute(array($id));
adminDeleteUploadIfUnused($pdo, $icon, 'tbl_why_feature', 'icon');

header('location: why-feature.php?deleted=1');
exit;
