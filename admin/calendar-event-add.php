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
	if (empty($_POST['event_date'])) {
		$valid = 0;
		$error_message .= 'Event date can not be empty<br>';
	}

	if ($valid == 1) {
		$statement = $pdo->prepare("
			INSERT INTO tbl_calendar_event (title, description, event_date, end_date, event_time, location, status, created_at)
			VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
		");
		$endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
		$statement->execute([
			strip_tags($_POST['title']),
			trim((string) ($_POST['description'] ?? '')),
			$_POST['event_date'],
			$endDate,
			strip_tags($_POST['event_time'] ?? ''),
			strip_tags($_POST['location'] ?? ''),
			$_POST['status'] ?? 'Active',
		]);
		$success_message = 'Calendar event added successfully.';
		unset($_POST);
	}
}
?>

<section class="content-header">
	<div class="content-header-left"><h1>Add Calendar Event</h1></div>
	<div class="content-header-right"><a href="calendar-event.php" class="btn btn-primary btn-sm">View All</a></div>
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
							<label class="col-sm-2 control-label">Start Date *</label>
							<div class="col-sm-4"><input type="date" class="form-control" name="event_date" value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>"></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">End Date</label>
							<div class="col-sm-4"><input type="date" class="form-control" name="end_date" value="<?php echo htmlspecialchars($_POST['end_date'] ?? ''); ?>"></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Time</label>
							<div class="col-sm-4"><input type="text" class="form-control" name="event_time" placeholder="e.g. 10:00 AM" value="<?php echo htmlspecialchars($_POST['event_time'] ?? ''); ?>"></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Location</label>
							<div class="col-sm-6"><input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-9"><textarea class="form-control" name="description" rows="5"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea></div>
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
