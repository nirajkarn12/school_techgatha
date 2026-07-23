<?php require_once('header.php'); ?>
<?php
$id = (int) ($_REQUEST['id'] ?? 0);
$albumId = (int) ($_REQUEST['album_id'] ?? 0);
if ($id <= 0 || $albumId <= 0) {
	header('location: gallery-album.php');
	exit;
}

$stmt = $pdo->prepare('SELECT * FROM tbl_gallery WHERE id = ? AND album_id = ?');
$stmt->execute([$id, $albumId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
	$path = '../assets/uploads/' . $row['photo'];
	if (!empty($row['photo']) && is_file($path)) {
		@unlink($path);
	}
	$pdo->prepare('DELETE FROM tbl_gallery WHERE id = ?')->execute([$id]);

	$album = $pdo->prepare('SELECT cover_photo FROM tbl_gallery_album WHERE id = ?');
	$album->execute([$albumId]);
	$albumRow = $album->fetch(PDO::FETCH_ASSOC);
	if ($albumRow && $albumRow['cover_photo'] === $row['photo']) {
		$next = $pdo->prepare('SELECT photo FROM tbl_gallery WHERE album_id = ? ORDER BY sort_order ASC, id ASC LIMIT 1');
		$next->execute([$albumId]);
		$nextPhoto = $next->fetchColumn();
		$pdo->prepare('UPDATE tbl_gallery_album SET cover_photo = ? WHERE id = ?')->execute([(string) ($nextPhoto ?: ''), $albumId]);
	}
}

header('location: gallery-album-edit.php?id=' . $albumId . '&photo_deleted=1');
exit;
