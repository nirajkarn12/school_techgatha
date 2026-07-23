<?php require_once('header.php'); ?>
<?php
if (!isset($_REQUEST['id'])) {
	header('location: admission-list.php');
	exit;
}
$id = (int) $_REQUEST['id'];
$statement = $pdo->prepare('DELETE FROM tbl_admission WHERE id = ?');
$statement->execute([$id]);
header('location: admission-list.php');
exit;
