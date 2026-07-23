<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Leadership Messages</h1>
	</div>
	<div class="content-header-right">
		<a href="run-school-migration.php" class="btn btn-warning btn-sm">Run DB Migration</a>
	</div>
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
								<th>Role</th>
								<th>Name</th>
								<th>Designation</th>
								<th>Status</th>
								<th width="100">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$labels = [
								'principal' => 'Principal',
								'chairman' => 'Chairman',
								'vice_principal' => 'Vice Principal',
							];
							$i = 0;
							try {
								$statement = $pdo->prepare("SELECT * FROM tbl_school_message ORDER BY sort_order ASC, id ASC");
								$statement->execute();
								$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							} catch (Throwable $e) {
								$result = [];
								echo '<tr><td colspan="6">Table missing. <a href="run-school-migration.php">Run school migration</a>.</td></tr>';
							}
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td><?php echo htmlspecialchars($labels[$row['role']] ?? $row['role']); ?></td>
									<td><?php echo htmlspecialchars($row['person_name']); ?></td>
									<td><?php echo htmlspecialchars($row['designation']); ?></td>
									<td><?php echo htmlspecialchars($row['status']); ?></td>
									<td>
										<a href="leadership-edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
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

<?php require_once('footer.php'); ?>
