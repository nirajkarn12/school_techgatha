<?php require_once('header.php'); ?>

<?php
if (isset($_POST['form1'])) {
	$valid = 1;
	$title = trim($_POST['title'] ?? '');
	$content = trim($_POST['content'] ?? '');
	$status = ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active';
	$sort_order = (int)($_POST['sort_order'] ?? 0);

	if ($title === '') {
		$valid = 0;
		$error_message .= 'Title can not be empty<br>';
	}

	$files = $_FILES['photos'] ?? null;
	$uploadNames = array();

	if ($files && is_array($files['name'])) {
		foreach ($files['name'] as $i => $name) {
			if ($name === '' || (int)($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
				continue;
			}
			if ((int)$files['error'][$i] !== UPLOAD_ERR_OK) {
				$valid = 0;
				$error_message .= 'Upload failed for ' . htmlspecialchars($name) . '<br>';
				continue;
			}
			$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
			if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
				$valid = 0;
				$error_message .= htmlspecialchars($name) . ' must be jpg, jpeg, png, gif or webp<br>';
				continue;
			}
			$uploadNames[] = array('name' => $name, 'ext' => $ext, 'tmp' => $files['tmp_name'][$i]);
		}
	}

	if (!$uploadNames) {
		$valid = 0;
		$error_message .= 'Please select at least one photo<br>';
	}

	if ($valid == 1) {
		try {
			$pdo->query("SELECT mcat_id FROM tbl_gallery LIMIT 1");
		} catch (Throwable $e) {
			$pdo->exec("ALTER TABLE `tbl_gallery` ADD COLUMN `mcat_id` int NOT NULL DEFAULT 0 AFTER `photo`");
		}

		$insert = $pdo->prepare("INSERT INTO tbl_gallery (title, content, photo, mcat_id, status, sort_order, created_at) VALUES (?, ?, ?, 0, ?, ?, NOW())");
		$saved = 0;
		$fileIndex = 0;

		foreach ($uploadNames as $file) {
			$fileIndex++;
			$final_name = adminUniqueUploadName('gallery', $file['ext']);
			if (!adminMoveUploadedFile($file['tmp'], $final_name)) {
				$error_message .= 'Could not save ' . htmlspecialchars($file['name']) . '<br>';
				continue;
			}

			$itemTitle = $title;
			if (count($uploadNames) > 1) {
				$itemTitle = $title . ' (' . $fileIndex . ')';
			}

			$insert->execute(array(
				$itemTitle,
				$content,
				$final_name,
				$status,
				$sort_order + ($fileIndex - 1),
			));
			$saved++;
		}

		if ($saved > 0) {
			header('location: gallery.php?success=1&count=' . $saved);
			exit;
		}

		$error_message .= 'No photos were saved<br>';
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Gallery Photos</h1>
	</div>
	<div class="content-header-right">
		<a href="gallery.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php if ($error_message): ?>
			<div class="callout callout-danger">
				<p><?php echo $error_message; ?></p>
			</div>
			<?php endif; ?>

			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Title <span>*</span></label>
							<div class="col-sm-6">
								<input type="text" autocomplete="off" class="form-control" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" placeholder="Shared title for uploaded photos">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Caption</label>
							<div class="col-sm-6">
								<textarea class="form-control" name="content" style="height:120px;"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Photos <span>*</span></label>
							<div class="col-sm-9" style="padding-top:5px">
								<input type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png,.gif,.webp">
								<small class="text-muted">Select one or more images (jpg, jpeg, png, gif, webp).</small>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Status</label>
							<div class="col-sm-3">
								<select name="status" class="form-control">
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Sort Order</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" name="sort_order" value="<?php echo isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Upload</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
