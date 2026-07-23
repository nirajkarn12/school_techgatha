<?php require_once('header.php'); ?>

<?php
if (isset($_GET['success'])) {
	$success_message = 'Gallery album saved successfully.';
} elseif (isset($_GET['deleted'])) {
	$success_message = 'Gallery album deleted successfully.';
}

$tableReady = true;
try {
	$pdo->query('SELECT 1 FROM tbl_gallery_album LIMIT 1');
} catch (Throwable $e) {
	$tableReady = false;
	$error_message = 'Album table missing. <a href="run-gallery-album-migration.php">Run album migration</a> first.';
}
?>

<section class="content-header">
	<div class="content-header-left"><h1>Gallery Albums</h1></div>
	<div class="content-header-right">
		<a href="run-gallery-album-migration.php" class="btn btn-warning btn-sm">Run Migration</a>
		<a href="gallery-album-add.php" class="btn btn-primary btn-sm">Add Album</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php if (!empty($error_message)) { ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php } ?>
			<?php if (!empty($success_message)) { ?><div class="callout callout-success"><p><?php echo $success_message; ?></p></div><?php } ?>

			<?php if ($tableReady) { ?>
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th width="30">#</th>
								<th width="90">Cover</th>
								<th>Album Title</th>
								<th width="90">Photos</th>
								<th width="80">Order</th>
								<th width="90">Status</th>
								<th width="140">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							$statement = $pdo->query("
								SELECT a.*,
									(SELECT COUNT(*) FROM tbl_gallery g WHERE g.album_id = a.id) AS photo_count
								FROM tbl_gallery_album a
								ORDER BY a.sort_order ASC, a.id DESC
							");
							foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
								$i++;
								$cover = !empty($row['cover_photo']) ? $row['cover_photo'] : '';
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td>
										<?php if ($cover !== '') { ?>
											<img src="../assets/uploads/<?php echo htmlspecialchars($cover); ?>" alt="" style="width:70px;height:50px;object-fit:cover;border-radius:4px;">
										<?php } else { ?>
											<span class="text-muted">No cover</span>
										<?php } ?>
									</td>
									<td><?php echo htmlspecialchars($row['title']); ?></td>
									<td><?php echo (int) $row['photo_count']; ?></td>
									<td><?php echo (int) $row['sort_order']; ?></td>
									<td><?php echo htmlspecialchars($row['status']); ?></td>
									<td>
										<a href="gallery-album-edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="gallery-album-delete.php?id=<?php echo (int)$row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php if ($i === 0) { ?>
						<p class="text-center text-muted" style="margin-top:12px;">No albums yet. <a href="gallery-album-add.php">Add album</a>.</p>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
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
			<div class="modal-body"><p>Delete this album and all photos inside it?</p></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a class="btn btn-danger btn-ok">Delete</a>
			</div>
		</div>
	</div>
</div>

<?php require_once('footer.php'); ?>
