<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

if (isset($_GET['success'])) {
    $success_message = 'Birthday template is added successfully!';
} elseif (isset($_GET['updated'])) {
    $success_message = 'Birthday template is updated successfully!';
} elseif (isset($_GET['deleted'])) {
    $success_message = 'Birthday template is deleted successfully!';
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Birthday Templates</h1></div>
    <div class="content-header-right"><a href="birthday-template-add.php" class="btn btn-primary btn-sm">Add Template</a></div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($error_message): ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php endif; ?>
            <?php if ($success_message): ?><div class="callout callout-success"><p><?php echo $success_message; ?></p></div><?php endif; ?>

            <div class="box box-info">
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="30">#</th>
                                <th width="90">Image</th>
                                <th>Title</th>
                                <th>Placement</th>
                                <th>Status</th>
                                <th width="140">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $statement = $pdo->prepare("SELECT * FROM tbl_birthday_template ORDER BY sort_order ASC, id DESC");
                            $statement->execute();
                            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
                                $i++;
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td>
                                        <?php if (!empty($row['template_image']) && is_file(adminUploadsPath($row['template_image']))): ?>
                                            <img src="<?php echo htmlspecialchars(adminUploadUrl($row['template_image'])); ?>" style="width:70px;height:70px;object-fit:cover;background:#f2f2f2;padding:4px;">
                                        <?php else: ?>
                                            <span class="text-muted">Missing</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo (int)$row['output_x']; ?>, <?php echo (int)$row['output_y']; ?> / <?php echo (int)$row['output_width']; ?>×<?php echo (int)$row['output_height']; ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <a href="birthday-template-edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
                                        <a href="#" class="btn btn-danger btn-xs" data-href="birthday-template-delete.php?id=<?php echo (int)$row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
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
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Confirmation</h4>
            </div>
            <div class="modal-body"><p>Are you sure want to delete this birthday template?</p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>