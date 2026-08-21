<?php require_once('header.php'); ?>

<?php
ensureAchieverTable($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT * FROM tbl_achiever WHERE id=?");
$statement->execute(array($id));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
if (!$result) {
    header('location: logout.php');
    exit;
}
$row = $result[0];
$name = $row['name'];
$photo = $row['photo'];
$achievement = $row['achievement'];
$year = $row['year'];
$sort_order = (int)$row['sort_order'];
$status = $row['status'];

if (isset($_POST['form1'])) {
    $valid = 1;
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $valid = 0;
        $error_message .= 'Name is required<br>';
    }

    $path = $_FILES['photo']['name'] ?? '';
    $path_tmp = $_FILES['photo']['tmp_name'] ?? '';
    $imgErr = (int)($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE);
    $ext = '';
    $hasNewPhoto = ($path !== '' && $imgErr === UPLOAD_ERR_OK && is_uploaded_file($path_tmp));
    if ($hasNewPhoto) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            $valid = 0;
            $error_message .= 'Photo must be jpg, jpeg, png, gif or webp<br>';
            $hasNewPhoto = false;
        }
    } elseif ($path !== '' && $imgErr !== UPLOAD_ERR_NO_FILE) {
        $valid = 0;
        $error_message .= 'Photo upload failed. Try a smaller file.<br>';
    }

    if ($valid == 1) {
        $final_name = $photo;
        if ($hasNewPhoto) {
            $final_name = adminUniqueUploadName('achiever', $ext, $id);
            if (!adminMoveUploadedFile($path_tmp, $final_name)) {
                $valid = 0;
                $error_message .= 'Could not save photo. Check uploads folder permissions.<br>';
                $final_name = $photo;
            }
        }

        if ($valid == 1) {
            $statement = $pdo->prepare("UPDATE tbl_achiever SET name=?, photo=?, achievement=?, year=?, sort_order=?, status=? WHERE id=?");
            $statement->execute(array(
                $name,
                $final_name,
                trim($_POST['achievement'] ?? ''),
                trim($_POST['year'] ?? ''),
                (int)($_POST['sort_order'] ?? 0),
                ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
                $id,
            ));
            if ($hasNewPhoto && $photo !== '' && $photo !== $final_name) {
                adminDeleteUploadIfUnused($pdo, $photo, 'tbl_achiever', 'photo', $id);
            }
            header('location: achiever.php?updated=1');
            exit;
        }
    }

    $achievement = trim($_POST['achievement'] ?? $achievement);
    $year = trim($_POST['year'] ?? $year);
    $sort_order = (int)($_POST['sort_order'] ?? $sort_order);
    $status = $_POST['status'] ?? $status;
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Edit Achiever</h1></div>
    <div class="content-header-right"><a href="achiever.php" class="btn btn-primary btn-sm">View All</a></div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($error_message): ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php endif; ?>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Name <span>*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Current Photo</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <?php if ($photo !== '' && is_file(adminUploadsPath($photo))): ?>
                                    <img src="<?php echo htmlspecialchars(adminUploadUrl($photo)); ?>" style="width:90px;height:120px;object-fit:cover;background:#c41230;">
                                <?php else: ?>
                                    <span class="text-muted">No photo on file — please upload a new one.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Change Photo</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="photo"> (optional)
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Achievement</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="achievement" value="<?php echo htmlspecialchars($achievement); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Year</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="year" value="<?php echo htmlspecialchars($year); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Sort Order</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="sort_order" value="<?php echo (int)$sort_order; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-3">
                                <select name="status" class="form-control">
                                    <option value="Active" <?php echo $status === 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo $status === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>
