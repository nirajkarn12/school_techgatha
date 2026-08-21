<?php require_once('header.php'); ?>
<?php
if (!isset($_REQUEST['id'])) {
	header('location: career-application.php');
	exit;
}
$id = (int) $_REQUEST['id'];
$pdo->prepare('DELETE FROM tbl_career_application WHERE id = ?')->execute([$id]);
header('location: career-application.php');
exit;
