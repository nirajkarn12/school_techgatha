<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left"><h1>Career Applications</h1></div>
	<div class="content-header-right"><a href="vacancy.php" class="btn btn-primary btn-sm">Vacancies</a></div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th width="30">#</th>
								<th>Applicant</th>
								<th>Vacancy</th>
								<th>Phone</th>
								<th>Email</th>
								<th>Status</th>
								<th>Submitted</th>
								<th width="160">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							try {
								$statement = $pdo->prepare("
									SELECT a.*, v.title AS vacancy_title
									FROM tbl_career_application a
									LEFT JOIN tbl_vacancy v ON v.id = a.vacancy_id
									ORDER BY a.id DESC
								");
								$statement->execute();
								$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							} catch (Throwable $e) {
								$result = [];
								echo '<tr><td colspan="8">Table missing. <a href="run-career-migration.php">Run career migration</a>.</td></tr>';
							}
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td><?php echo htmlspecialchars($row['full_name']); ?></td>
									<td><?php echo htmlspecialchars((string) $row['vacancy_title']); ?></td>
									<td><?php echo htmlspecialchars($row['phone']); ?></td>
									<td><?php echo htmlspecialchars($row['email']); ?></td>
									<td><?php echo htmlspecialchars($row['status']); ?></td>
									<td><?php echo htmlspecialchars((string) $row['created_at']); ?></td>
									<td>
										<a href="career-application-view.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-primary btn-xs">View</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="career-application-delete.php?id=<?php echo (int)$row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Delete Confirmation</h4>
			</div>
			<div class="modal-body"><p>Are you sure want to delete this application?</p></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a class="btn btn-danger btn-ok">Delete</a>
			</div>
		</div>
	</div>
</div>

<?php require_once('footer.php'); ?>
