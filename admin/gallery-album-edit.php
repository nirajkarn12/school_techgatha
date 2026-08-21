<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: gallery-album.php');
	exit;
}
$id = (int) $_REQUEST['id'];
$statement = $pdo->prepare('SELECT * FROM tbl_gallery_album WHERE id = ?');
$statement->execute([$id]);
$album = $statement->fetch(PDO::FETCH_ASSOC);
if (!$album) {
	header('location: gallery-album.php');
	exit;
}

$title = $album['title'];
$description = $album['description'];
$status = $album['status'];
$sort_order = (int) $album['sort_order'];
$cover_photo = $album['cover_photo'];
$error_message = '';
$success_message = '';

if (isset($_GET['photo_deleted'])) {
	$success_message = 'Photo removed from album.';
}

if (isset($_POST['form1'])) {
	$valid = 1;
	$title = trim((string) ($_POST['title'] ?? ''));
	$description = trim((string) ($_POST['description'] ?? ''));
	$status = ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active';
	$sort_order = (int) ($_POST['sort_order'] ?? 0);
	$cover_photo = trim((string) ($_POST['cover_photo'] ?? $cover_photo));

	if ($title === '') {
		$valid = 0;
		$error_message .= 'Album title can not be empty<br>';
	}

	if ($valid == 1) {
		$pdo->prepare("
			UPDATE tbl_gallery_album
			SET title=?, description=?, cover_photo=?, status=?, sort_order=?
			WHERE id=?
		")->execute([$title, $description, $cover_photo, $status, $sort_order, $id]);

		$files = $_FILES['photos'] ?? null;
		$uploadNames = [];
		if ($files && is_array($files['name'])) {
			foreach ($files['name'] as $i => $name) {
				if ($name === '' || (int) ($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
					continue;
				}
				if ((int) $files['error'][$i] !== UPLOAD_ERR_OK) {
					continue;
				}
				$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
				if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
					continue;
				}
				$uploadNames[] = ['ext' => $ext, 'tmp' => $files['tmp_name'][$i]];
			}
		}

		if ($uploadNames) {
			$maxSort = (int) $pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM tbl_gallery WHERE album_id = " . (int) $id)->fetchColumn();
			$insertPhoto = $pdo->prepare("
				INSERT INTO tbl_gallery (album_id, title, content, photo, mcat_id, status, sort_order, created_at)
				VALUES (?, ?, '', ?, 0, ?, ?, NOW())
			");
			$fileIndex = 0;
			foreach ($uploadNames as $file) {
				$fileIndex++;
				$finalName = 'gallery-album-' . $id . '-' . time() . '-' . $fileIndex . '.' . $file['ext'];
				if (!move_uploaded_file($file['tmp'], '../assets/uploads/' . $finalName)) {
					continue;
				}
				if ($cover_photo === '') {
					$cover_photo = $finalName;
					$pdo->prepare('UPDATE tbl_gallery_album SET cover_photo = ? WHERE id = ?')->execute([$cover_photo, $id]);
				}
				$insertPhoto->execute([
					$id,
					$title . ' (' . ($maxSort + $fileIndex) . ')',
					$finalName,
					$status,
					$maxSort + $fileIndex,
				]);
			}
		}

		$success_message = 'Album updated successfully.';
		$album['title'] = $title;
		$album['description'] = $description;
		$album['status'] = $status;
		$album['sort_order'] = $sort_order;
		$album['cover_photo'] = $cover_photo;
	}
}

$photos = $pdo->prepare('SELECT * FROM tbl_gallery WHERE album_id = ? ORDER BY sort_order ASC, id ASC');
$photos->execute([$id]);
$photos = $photos->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="content-header">
	<div class="content-header-left"><h1>Edit Gallery Album</h1></div>
	<div class="content-header-right"><a href="gallery-album.php" class="btn btn-primary btn-sm">View All</a></div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php if ($error_message) { ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php } ?>
			<?php if ($success_message) { ?><div class="callout callout-success"><p><?php echo $success_message; ?></p></div><?php } ?>

			<form class="form-horizontal" method="post" enctype="multipart/form-data">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">Album Title *</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($title); ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-8">
								<textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars((string) $description); ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Add More Photos</label>
							<div class="col-sm-6">
								<input type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png,.gif,.webp">
								<p class="help-block">Upload multiple photos at once into this album.</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Sort Order</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" name="sort_order" value="<?php echo (int) $sort_order; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-3">
								<select name="status" class="form-control">
									<option value="Active" <?php echo $status === 'Active' ? 'selected' : ''; ?>>Active</option>
									<option value="Inactive" <?php echo $status === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Cover Photo</label>
							<div class="col-sm-9">
								<?php if (!$photos) { ?>
									<p class="text-muted">No photos in this album yet.</p>
									<input type="hidden" name="cover_photo" value="">
								<?php } else { ?>
									<div class="row">
										<?php foreach ($photos as $photo) { ?>
											<div class="col-xs-6 col-sm-3" style="margin-bottom:12px;">
												<label style="display:block;border:1px solid #ddd;padding:6px;border-radius:4px;cursor:pointer;">
													<img src="../assets/uploads/<?php echo htmlspecialchars($photo['photo']); ?>" alt="" style="width:100%;height:90px;object-fit:cover;margin-bottom:6px;">
													<input type="radio" name="cover_photo" value="<?php echo htmlspecialchars($photo['photo']); ?>" <?php echo ($cover_photo === $photo['photo']) ? 'checked' : ''; ?>>
													Set as cover
												</label>
												<a href="gallery-album-photo-delete.php?id=<?php echo (int)$photo['id']; ?>&album_id=<?php echo (int)$id; ?>" class="btn btn-danger btn-xs btn-block" onclick="return confirm('Remove this photo?');">Remove</a>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="col-sm-6"><button type="submit" class="btn btn-success" name="form1">Update Album</button></div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
