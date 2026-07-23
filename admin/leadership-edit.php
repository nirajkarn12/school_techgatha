<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: leadership.php');
	exit;
}

$id = (int) $_REQUEST['id'];
$statement = $pdo->prepare('SELECT * FROM tbl_school_message WHERE id = ?');
$statement->execute([$id]);
$row = $statement->fetch(PDO::FETCH_ASSOC);
if (!$row) {
	header('location: leadership.php');
	exit;
}

$person_name = $row['person_name'];
$designation = $row['designation'];
$photo = $row['photo'];
$message = $row['message'];
$status = $row['status'];
$role = $row['role'];

$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {
	$valid = 1;
	if (trim((string) ($_POST['person_name'] ?? '')) === '') {
		$valid = 0;
		$error_message .= 'Name can not be empty<br>';
	}
	if (trim((string) ($_POST['message'] ?? '')) === '') {
		$valid = 0;
		$error_message .= 'Message can not be empty<br>';
	}

	if ($valid == 1) {
		if (!empty($_FILES['photo']['name'])) {
			$ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
			if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
				if ($photo && file_exists('../assets/uploads/' . $photo)) {
					@unlink('../assets/uploads/' . $photo);
				}
				$photo = 'leadership-' . $id . '-' . time() . '.' . $ext;
				move_uploaded_file($_FILES['photo']['tmp_name'], '../assets/uploads/' . $photo);
			}
		}

		$statement = $pdo->prepare("
			UPDATE tbl_school_message
			SET person_name=?, designation=?, photo=?, message=?, status=?, updated_at=NOW()
			WHERE id=?
		");
		$statement->execute([
			strip_tags($_POST['person_name']),
			strip_tags($_POST['designation'] ?? ''),
			$photo,
			$_POST['message'],
			$_POST['status'] ?? 'Active',
			$id,
		]);

		$person_name = $_POST['person_name'];
		$designation = $_POST['designation'] ?? '';
		$message = $_POST['message'];
		$status = $_POST['status'] ?? 'Active';
		$success_message = 'Leadership message updated successfully.';
	}
}

$roleLabels = [
	'principal' => 'Principal',
	'chairman' => 'Chairman',
	'vice_principal' => 'Vice Principal',
];
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit <?php echo htmlspecialchars($roleLabels[$role] ?? $role); ?> Message</h1>
	</div>
	<div class="content-header-right">
		<a href="leadership.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php if ($error_message) { ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php } ?>
			<?php if ($success_message) { ?><div class="callout callout-success"><p><?php echo $success_message; ?></p></div><?php } ?>

			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">Name *</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="person_name" value="<?php echo htmlspecialchars($person_name); ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Designation</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="designation" value="<?php echo htmlspecialchars($designation); ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Current Photo</label>
							<div class="col-sm-4">
								<?php if ($photo) { ?>
									<img src="../assets/uploads/<?php echo htmlspecialchars($photo); ?>" alt="" style="max-width:140px;">
								<?php } else { ?>
									<em>No photo</em>
								<?php } ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Change Photo</label>
							<div class="col-sm-4">
								<input type="file" name="photo">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Message *</label>
							<div class="col-sm-9">
								<textarea class="form-control" name="message" id="editor1" style="height:220px;"><?php echo htmlspecialchars($message); ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-4">
								<select name="status" class="form-control">
									<option value="Active" <?php echo $status === 'Active' ? 'selected' : ''; ?>>Active</option>
									<option value="Inactive" <?php echo $status === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
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
