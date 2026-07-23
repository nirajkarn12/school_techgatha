<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: career-application.php');
	exit;
}
$id = (int) $_REQUEST['id'];
$statement = $pdo->prepare("
	SELECT a.*, v.title AS vacancy_title
	FROM tbl_career_application a
	LEFT JOIN tbl_vacancy v ON v.id = a.vacancy_id
	WHERE a.id = ?
");
$statement->execute([$id]);
$row = $statement->fetch(PDO::FETCH_ASSOC);
if (!$row) {
	header('location: career-application.php');
	exit;
}

$success_message = '';
if (isset($_POST['form1'])) {
	$status = $_POST['status'] ?? 'New';
	$pdo->prepare('UPDATE tbl_career_application SET status = ? WHERE id = ?')->execute([$status, $id]);
	$row['status'] = $status;
	$success_message = 'Status updated.';
}
?>

<section class="content-header">
	<div class="content-header-left"><h1>Career Application</h1></div>
	<div class="content-header-right"><a href="career-application.php" class="btn btn-primary btn-sm">Back</a></div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-8">
			<?php if ($success_message) { ?><div class="callout callout-success"><p><?php echo $success_message; ?></p></div><?php } ?>
			<div class="box box-info">
				<div class="box-body">
					<table class="table table-bordered">
						<tr><th width="220">Vacancy</th><td><?php echo htmlspecialchars((string) $row['vacancy_title']); ?></td></tr>
						<tr><th>Name</th><td><?php echo htmlspecialchars($row['full_name']); ?></td></tr>
						<tr><th>Phone</th><td><?php echo htmlspecialchars($row['phone']); ?></td></tr>
						<tr><th>Email</th><td><?php echo htmlspecialchars($row['email']); ?></td></tr>
						<tr><th>Experience / Resume</th><td><?php echo nl2br(htmlspecialchars((string) $row['resume_note'])); ?></td></tr>
						<tr><th>Cover letter</th><td><?php echo nl2br(htmlspecialchars((string) $row['cover_letter'])); ?></td></tr>
						<tr><th>Submitted</th><td><?php echo htmlspecialchars((string) $row['created_at']); ?></td></tr>
					</table>
					<form method="post" class="form-inline" style="margin-top:15px;">
						<label>Status</label>
						<select name="status" class="form-control" style="margin:0 10px;">
							<?php foreach (['New', 'Reviewed', 'Shortlisted', 'Rejected', 'Hired'] as $st) { ?>
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
