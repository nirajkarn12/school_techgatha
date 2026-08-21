<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';
if (isset($_POST['form1'])) {
	$valid = 1;
	if (trim((string) ($_POST['name'] ?? '')) === '') {
		$valid = 0;
		$error_message .= 'Level name can not be empty<br>';
	}
	if ($valid == 1) {
		$statement = $pdo->prepare("
			INSERT INTO tbl_teacher_level (name, sort_order, status, created_at)
			VALUES (?, ?, ?, NOW())
		");
		$statement->execute([
			strip_tags($_POST['name']),
			(int) ($_POST['sort_order'] ?? 0),
			$_POST['status'] ?? 'Active',
		]);
		$success_message = 'Teacher level added successfully.';
		unset($_POST);
	}
}
?>

<section class="content-header">
	<div class="content-header-left"><h1>Add Teacher Level</h1></div>
	<div class="content-header-right"><a href="teacher-level.php" class="btn btn-primary btn-sm">View All</a></div>
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
							<label class="col-sm-2 control-label">Level Name *</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="e.g. Primary Level">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Sort Order</label>
							<div class="col-sm-4">
								<input type="number" class="form-control" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? '0'); ?>">
								<p class="help-block">Lower number shows first on the team page.</p>
							</div>
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
