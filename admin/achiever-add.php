<?php require_once('header.php'); ?>

<?php
ensureAchieverTable($pdo);

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
    if ($path === '' || $imgErr !== UPLOAD_ERR_OK || !is_uploaded_file($path_tmp)) {
        $valid = 0;
        $error_message .= 'Photo is required<br>';
    } else {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            $valid = 0;
            $error_message .= 'Photo must be jpg, jpeg, png, gif or webp<br>';
        }
    }

    if ($valid == 1) {
        $final_name = adminUniqueUploadName('achiever', $ext);
        if (!adminMoveUploadedFile($path_tmp, $final_name)) {
            $error_message .= 'Could not save photo. Check uploads folder permissions.<br>';
        } else {
            $statement = $pdo->prepare("INSERT INTO tbl_achiever (name, photo, achievement, year, sort_order, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $statement->execute(array(
                $name,
                $final_name,
                trim($_POST['achievement'] ?? ''),
                trim($_POST['year'] ?? ''),
                (int)($_POST['sort_order'] ?? 0),
                ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
            ));
            header('location: achiever.php?success=1');
            exit;
        }
    }
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Add Achiever</h1></div>
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
                                <input type="text" class="form-control" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Photo <span>*</span></label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="photo"> (portrait photo works best)
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Achievement</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="achievement" value="<?php echo isset($_POST['achievement']) ? htmlspecialchars($_POST['achievement']) : ''; ?>" placeholder="SEE Outstanding Achiever (GPA 4)">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Year</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="year" value="<?php echo isset($_POST['year']) ? htmlspecialchars($_POST['year']) : date('Y'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Sort Order</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="sort_order" value="<?php echo isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-3">
                                <select name="status" class="form-control">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>
