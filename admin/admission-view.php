<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: admission-list.php');
	exit;
}

$id = (int) $_REQUEST['id'];
$statement = $pdo->prepare('SELECT * FROM tbl_admission WHERE id = ?');
$statement->execute([$id]);
$row = $statement->fetch(PDO::FETCH_ASSOC);
if (!$row) {
	header('location: admission-list.php');
	exit;
}

$success_message = '';
if (isset($_POST['form1'])) {
	$status = $_POST['status'] ?? 'New';
	$statement = $pdo->prepare('UPDATE tbl_admission SET status = ? WHERE id = ?');
	$statement->execute([$status, $id]);
	$row['status'] = $status;
	$success_message = 'Status updated.';
}
?>

<section class="content-header">
	<div class="content-header-left"><h1>Admission Application</h1></div>
	<div class="content-header-right"><a href="admission-list.php" class="btn btn-primary btn-sm">Back</a></div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-8">
			<?php if ($success_message) { ?><div class="callout callout-success"><p><?php echo $success_message; ?></p></div><?php } ?>
			<div class="box box-info">
				<div class="box-body">
					<table class="table table-bordered">
						<tr><th width="220">Student name</th><td><?php echo htmlspecialchars($row['student_name']); ?></td></tr>
						<tr><th>Date of birth</th><td><?php echo htmlspecialchars((string) $row['dob']); ?></td></tr>
						<tr><th>Gender</th><td><?php echo htmlspecialchars($row['gender']); ?></td></tr>
						<tr><th>Class applied</th><td><?php echo htmlspecialchars($row['class_applied']); ?></td></tr>
						<tr><th>Previous school</th><td><?php echo htmlspecialchars($row['previous_school']); ?></td></tr>
						<tr><th>Parent / Guardian</th><td><?php echo htmlspecialchars($row['parent_name']); ?></td></tr>
						<tr><th>Phone</th><td><?php echo htmlspecialchars($row['phone']); ?></td></tr>
						<tr><th>Email</th><td><?php echo htmlspecialchars($row['email']); ?></td></tr>
						<tr><th>Address</th><td><?php echo nl2br(htmlspecialchars((string) $row['address'])); ?></td></tr>
						<tr><th>Message</th><td><?php echo nl2br(htmlspecialchars((string) $row['message'])); ?></td></tr>
						<tr><th>Submitted</th><td><?php echo htmlspecialchars((string) $row['created_at']); ?></td></tr>
					</table>

					<form method="post" class="form-inline" style="margin-top:15px;">
						<label>Status</label>
						<select name="status" class="form-control" style="margin:0 10px;">
							<?php foreach (['New', 'Reviewed', 'Contacted', 'Accepted', 'Rejected'] as $st) { ?>
								<option value="<?php echo $st; ?>" <?php echo $row['status'] === $st ? 'selected' : ''; ?>><?php echo $st; ?></option>
							<?php } ?>
						</select>
						<button type="submit" class="btn btn-success" name="form1">Update status</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
