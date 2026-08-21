<?php require_once('header.php'); ?>
<?php
if (!isset($_REQUEST['id'])) {
	header('location: vacancy.php');
	exit;
}
$id = (int) $_REQUEST['id'];
$pdo->prepare('DELETE FROM tbl_career_application WHERE vacancy_id = ?')->execute([$id]);
$pdo->prepare('DELETE FROM tbl_vacancy WHERE id = ?')->execute([$id]);
header('location: vacancy.php');
exit;
