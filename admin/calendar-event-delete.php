<?php require_once('header.php'); ?>
<?php
if (!isset($_REQUEST['id'])) {
	header('location: calendar-event.php');
	exit;
}
$id = (int) $_REQUEST['id'];
$statement = $pdo->prepare('DELETE FROM tbl_calendar_event WHERE id = ?');
$statement->execute([$id]);
header('location: calendar-event.php');
exit;
