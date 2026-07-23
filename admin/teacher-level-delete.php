<?php require_once('header.php'); ?>
<?php
if (!isset($_REQUEST['id'])) {
	header('location: teacher-level.php');
	exit;
}
$id = (int) $_REQUEST['id'];
try {
	$pdo->prepare('UPDATE tbl_staff SET level_id = NULL WHERE level_id = ?')->execute([$id]);
	$pdo->prepare('DELETE FROM tbl_teacher_level WHERE id = ?')->execute([$id]);
} catch (Throwable $e) {
	// ignore and redirect
}
header('location: teacher-level.php');
exit;
