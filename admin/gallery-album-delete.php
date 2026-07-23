<?php require_once('header.php'); ?>
<?php
if (!isset($_REQUEST['id'])) {
	header('location: gallery-album.php');
	exit;
}
$id = (int) $_REQUEST['id'];

try {
	$photos = $pdo->prepare('SELECT photo FROM tbl_gallery WHERE album_id = ?');
	$photos->execute([$id]);
	foreach ($photos->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$path = '../assets/uploads/' . $row['photo'];
		if (!empty($row['photo']) && is_file($path)) {
			@unlink($path);
		}
	}
	$pdo->prepare('DELETE FROM tbl_gallery WHERE album_id = ?')->execute([$id]);
	$pdo->prepare('DELETE FROM tbl_gallery_album WHERE id = ?')->execute([$id]);
} catch (Throwable $e) {
	// redirect anyway
}

header('location: gallery-album.php?deleted=1');
exit;
