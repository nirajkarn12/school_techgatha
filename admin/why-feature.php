<?php require_once('header.php'); ?>

<?php
ensureWhyFeatureTable($pdo);

if (isset($_GET['success'])) {
    $success_message = 'Feature is added successfully!';
} elseif (isset($_GET['updated'])) {
    $success_message = 'Feature is updated successfully!';
} elseif (isset($_GET['deleted'])) {
    $success_message = 'Feature is deleted successfully!';
} elseif (isset($_GET['hero'])) {
    $success_message = 'Center image is updated successfully!';
}

if (isset($_POST['form_hero'])) {
    $path = $_FILES['hero_photo']['name'] ?? '';
    $path_tmp = $_FILES['hero_photo']['tmp_name'] ?? '';
    if ($path !== '') {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            foreach (array('png', 'jpg', 'jpeg', 'webp', 'gif') as $oldExt) {
                $old = '../assets/uploads/why-choose-hero.' . $oldExt;
                if (is_file($old)) {
                    @unlink($old);
                }
            }
            $final = 'why-choose-hero.' . $ext;
            if (!adminMoveUploadedFile($path_tmp, $final)) {
                $error_message = 'Could not save center image. Check uploads folder permissions.';
            } else {
                header('location: why-feature.php?hero=1');
                exit;
            }
        }
        $error_message = 'Center image must be jpg, jpeg, png, gif or webp';
    } else {
        $error_message = 'Please choose a center image file.';
    }
}

$heroPreview = '';
foreach (array('png', 'jpg', 'jpeg', 'webp', 'gif') as $ext) {
    if (is_file('../assets/uploads/why-choose-hero.' . $ext)) {
        $heroPreview = '../assets/uploads/why-choose-hero.' . $ext;
        break;
    }
}
if ($heroPreview === '' && is_file('../assets/hero.png')) {
    $heroPreview = '../assets/hero.png';
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Why Choose Features</h1></div>
    <div class="content-header-right"><a href="why-feature-add.php" class="btn btn-primary btn-sm">Add Feature</a></div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($error_message): ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php endif; ?>
            <?php if ($success_message): ?><div class="callout callout-success"><p><?php echo $success_message; ?></p></div><?php endif; ?>

            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">Center Student Image (assets/hero.png)</h3></div>
                <div class="box-body">
                    <form class="form-horizontal" method="post" enctype="multipart/form-data">
                        <?php if ($heroPreview): ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Current</label>
                            <div class="col-sm-6">
                                <img src="<?php echo htmlspecialchars(strpos($heroPreview, 'assets/uploads/') !== false ? adminUploadUrl(basename($heroPreview)) : ($heroPreview . '?v=' . @filemtime($heroPreview))); ?>" alt="" style="max-height:180px;background:#0a3d7a;padding:8px;">
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Upload PNG cutout</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="hero_photo" accept=".jpg,.jpeg,.png,.gif,.webp">
                                <p class="help-block">Transparent PNG works best. Falls back to <code>assets/hero.png</code>.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success" name="form_hero">Update Image</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="30">#</th>
                                <th width="90">Icon</th>
                                <th>Title</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th width="140">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $statement = $pdo->prepare("SELECT * FROM tbl_why_feature ORDER BY sort_order ASC, id ASC");
                            $statement->execute();
                            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
                                $i++;
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td>
                                        <?php if (!empty($row['icon']) && is_file('../assets/uploads/' . $row['icon'])): ?>
                                            <img src="../assets/uploads/<?php echo htmlspecialchars($row['icon']); ?>" style="max-width:48px;max-height:48px;">
                                        <?php else: ?>
                                            <i class="fa <?php echo htmlspecialchars($row['icon_class'] ?: 'fa-star'); ?>"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo (int)$row['sort_order']; ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <a href="why-feature-edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
                                        <a href="#" class="btn btn-danger btn-xs" data-href="why-feature-delete.php?id=<?php echo (int)$row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
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
            <div class="modal-body"><p>Are you sure want to delete this feature?</p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
