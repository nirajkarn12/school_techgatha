<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';
if (isset($_POST['form1'])) {
	$valid = 1;
	if (trim((string) ($_POST['title'] ?? '')) === '') {
		$valid = 0;
		$error_message .= 'Title can not be empty<br>';
	}
	if ($valid == 1) {
		$deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
		$statement = $pdo->prepare("
			INSERT INTO tbl_vacancy (title, department, description, deadline, status, created_at)
			VALUES (?, ?, ?, ?, ?, NOW())
		");
		$statement->execute([
			strip_tags($_POST['title']),
			strip_tags($_POST['department'] ?? ''),
			trim((string) ($_POST['description'] ?? '')),
			$deadline,
			$_POST['status'] ?? 'Active',
		]);
		$success_message = 'Vacancy added successfully.';
		unset($_POST);
	}
}
?>

<section class="content-header">
	<div class="content-header-left"><h1>Add Vacancy</h1></div>
	<div class="content-header-right"><a href="vacancy.php" class="btn btn-primary btn-sm">View All</a></div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php if ($error_message) { ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php } ?>
			<?php if ($success_message) { ?><div class="callout callout-success"><p><?php echo $success_message; ?></p></div><?php } ?>
			<form class="form-horizontal" method="post">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">Title *</label>
							<div class="col-sm-6"><input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Department</label>
							<div class="col-sm-6"><input type="text" class="form-control" name="department" value="<?php echo htmlspecialchars($_POST['department'] ?? ''); ?>"></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Deadline</label>
							<div class="col-sm-4"><input type="date" class="form-control" name="deadline" value="<?php echo htmlspecialchars($_POST['deadline'] ?? ''); ?>"></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-9"><textarea class="form-control" name="description" rows="6"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-4">
								<select name="status" class="form-control">
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="col-sm-6"><button type="submit" class="btn btn-success" name="form1">Submit</button></div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
