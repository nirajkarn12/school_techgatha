<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {
	$valid = 1;
	$title = trim((string) ($_POST['title'] ?? ''));
	$description = trim((string) ($_POST['description'] ?? ''));
	$status = ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active';
	$sort_order = (int) ($_POST['sort_order'] ?? 0);

	if ($title === '') {
		$valid = 0;
		$error_message .= 'Album title can not be empty<br>';
	}

	$uploadNames = [];
	$files = $_FILES['photos'] ?? null;
	if ($files && is_array($files['name'])) {
		foreach ($files['name'] as $i => $name) {
			if ($name === '' || (int) ($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
				continue;
			}
			if ((int) $files['error'][$i] !== UPLOAD_ERR_OK) {
				$valid = 0;
				$error_message .= 'Upload failed for ' . htmlspecialchars($name) . '<br>';
				continue;
			}
			$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
			if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
				$valid = 0;
				$error_message .= htmlspecialchars($name) . ' must be jpg, jpeg, png, gif or webp<br>';
				continue;
			}
			$uploadNames[] = [
				'name' => $name,
				'ext' => $ext,
				'tmp' => $files['tmp_name'][$i],
			];
		}
	}

	if (!$uploadNames) {
		$valid = 0;
		$error_message .= 'Please select at least one photo<br>';
	}

	if ($valid == 1) {
		try {
			$pdo->beginTransaction();
			$pdo->prepare("
				INSERT INTO tbl_gallery_album (title, description, cover_photo, status, sort_order, created_at)
				VALUES (?, ?, '', ?, ?, NOW())
			")->execute([$title, $description, $status, $sort_order]);
			$albumId = (int) $pdo->lastInsertId();

			$insertPhoto = $pdo->prepare("
				INSERT INTO tbl_gallery (album_id, title, content, photo, mcat_id, status, sort_order, created_at)
				VALUES (?, ?, '', ?, 0, ?, ?, NOW())
			");
			$cover = '';
			$fileIndex = 0;
			foreach ($uploadNames as $file) {
				$fileIndex++;
				$finalName = 'gallery-album-' . $albumId . '-' . time() . '-' . $fileIndex . '.' . $file['ext'];
				if (!move_uploaded_file($file['tmp'], '../assets/uploads/' . $finalName)) {
					throw new RuntimeException('Could not save ' . $file['name']);
				}
				if ($cover === '') {
					$cover = $finalName;
				}
				$insertPhoto->execute([
					$albumId,
					$title . (count($uploadNames) > 1 ? ' (' . $fileIndex . ')' : ''),
					$finalName,
					$status,
					$fileIndex,
				]);
			}

			$pdo->prepare('UPDATE tbl_gallery_album SET cover_photo = ? WHERE id = ?')->execute([$cover, $albumId]);
			$pdo->commit();
			header('location: gallery-album.php?success=1');
			exit;
		} catch (Throwable $e) {
			if ($pdo->inTransaction()) {
				$pdo->rollBack();
			}
			$error_message .= 'Could not save album. Run migration if needed.<br>' . htmlspecialchars($e->getMessage());
		}
	}
}
?>

<section class="content-header">
	<div class="content-header-left"><h1>Add Gallery Album</h1></div>
	<div class="content-header-right"><a href="gallery-album.php" class="btn btn-primary btn-sm">View All</a></div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php if ($error_message) { ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php } ?>
			<form class="form-horizontal" method="post" enctype="multipart/form-data">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">Album Title *</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" placeholder="e.g. Zoo Visit | Grade One - 2082">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-8">
								<textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Photos *</label>
							<div class="col-sm-6">
								<input type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png,.gif,.webp">
								<p class="help-block">Select multiple photos at once. First photo becomes the album cover.</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Sort Order</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? '0'); ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-3">
								<select name="status" class="form-control">
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="col-sm-6"><button type="submit" class="btn btn-success" name="form1">Create Album</button></div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
