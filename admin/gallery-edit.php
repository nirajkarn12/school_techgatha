<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT * FROM tbl_gallery WHERE id=?");
$statement->execute(array($id));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
if (!$result) {
	header('location: logout.php');
	exit;
}

$row = $result[0];
$title = $row['title'];
$content = $row['content'];
$photo = $row['photo'];
$status = $row['status'];
$sort_order = $row['sort_order'];

if (isset($_POST['form1'])) {
	$valid = 1;

	if (empty($_POST['title'])) {
		$valid = 0;
		$error_message .= 'Title can not be empty<br>';
	}

	$path = $_FILES['photo']['name'] ?? '';
	$path_tmp = $_FILES['photo']['tmp_name'] ?? '';
	$imgErr = (int)($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE);
	$ext = '';
	$hasNewPhoto = ($path !== '' && $imgErr === UPLOAD_ERR_OK && is_uploaded_file($path_tmp));

	if ($hasNewPhoto) {
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
			$valid = 0;
			$error_message .= 'You must upload a jpg, jpeg, png, gif or webp file<br>';
			$hasNewPhoto = false;
		}
	}

	if ($valid == 1) {
		$title = trim($_POST['title']);
		$content = trim($_POST['content'] ?? '');
		$status = ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active';
		$sort_order = (int)($_POST['sort_order'] ?? 0);
		$final_name = $photo;

		if ($hasNewPhoto) {
			$final_name = adminUniqueUploadName('gallery', $ext, $id);
			if (!adminMoveUploadedFile($path_tmp, $final_name)) {
				$valid = 0;
				$error_message .= 'Could not save photo. Check uploads folder permissions.<br>';
				$final_name = $photo;
			}
		}

		if ($valid == 1) {
			$statement = $pdo->prepare("UPDATE tbl_gallery SET title=?, content=?, photo=?, status=?, sort_order=? WHERE id=?");
			$statement->execute(array($title, $content, $final_name, $status, $sort_order, $id));
			if ($hasNewPhoto && $photo !== '' && $photo !== $final_name) {
				adminDeleteUploadIfUnused($pdo, $photo, 'tbl_gallery', 'photo', $id);
			}
			header('location: gallery.php?updated=1');
			exit;
		}
	}

	$title = trim($_POST['title'] ?? $title);
	$content = trim($_POST['content'] ?? $content);
	$status = $_POST['status'] ?? $status;
	$sort_order = (int)($_POST['sort_order'] ?? $sort_order);
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Gallery Photo</h1>
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
				<input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($photo); ?>">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Title <span>*</span></label>
							<div class="col-sm-6">
								<input type="text" autocomplete="off" class="form-control" name="title" value="<?php echo htmlspecialchars($title); ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Caption</label>
							<div class="col-sm-6">
								<textarea class="form-control" name="content" style="height:140px;"><?php echo htmlspecialchars((string)$content); ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Existing Photo</label>
							<div class="col-sm-9" style="padding-top:5px">
								<?php if ($photo !== '' && is_file(adminUploadsPath($photo))) { ?>
									<img src="<?php echo htmlspecialchars(adminUploadUrl($photo)); ?>" alt="Gallery Photo" style="width:220px;">
								<?php } else { ?>
									<span class="label label-default">No photo</span>
								<?php } ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Change Photo</label>
							<div class="col-sm-6" style="padding-top:5px">
								<input type="file" name="photo"> (jpg, jpeg, png, gif, webp)
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Status</label>
							<div class="col-sm-3">
								<select name="status" class="form-control">
									<option value="Active" <?php if ($status === 'Active') echo 'selected'; ?>>Active</option>
									<option value="Inactive" <?php if ($status === 'Inactive') echo 'selected'; ?>>Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Sort Order</label>
							<div class="col-sm-3">
								<input type="number" class="form-control" name="sort_order" value="<?php echo (int)$sort_order; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
