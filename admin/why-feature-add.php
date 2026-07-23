<?php require_once('header.php'); ?>

<?php
ensureWhyFeatureTable($pdo);

if (isset($_POST['form1'])) {
    $valid = 1;
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        $valid = 0;
        $error_message .= 'Title is required<br>';
    }

    $final_icon = '';
    $path = $_FILES['icon']['name'] ?? '';
    $path_tmp = $_FILES['icon']['tmp_name'] ?? '';
    $imgErr = (int)($_FILES['icon']['error'] ?? UPLOAD_ERR_NO_FILE);
    $ext = '';
    $hasIcon = ($path !== '' && $imgErr === UPLOAD_ERR_OK && is_uploaded_file($path_tmp));
    if ($hasIcon) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'), true)) {
            $valid = 0;
            $error_message .= 'Icon must be jpg, jpeg, png, gif, webp or svg<br>';
            $hasIcon = false;
        }
    }

    if ($valid == 1) {
        if ($hasIcon) {
            $final_icon = adminUniqueUploadName('why-icon', $ext);
            if (!adminMoveUploadedFile($path_tmp, $final_icon)) {
                $valid = 0;
                $error_message .= 'Could not save icon. Check uploads folder permissions.<br>';
                $final_icon = '';
            }
        }

        if ($valid == 1) {
            $statement = $pdo->prepare("INSERT INTO tbl_why_feature (title, icon, icon_class, sort_order, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $statement->execute(array(
                $title,
                $final_icon,
                trim($_POST['icon_class'] ?? 'fa-star') ?: 'fa-star',
                (int)($_POST['sort_order'] ?? 0),
                ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
            ));
            header('location: why-feature.php?success=1');
            exit;
        }
    }
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Add Why Choose Feature</h1></div>
    <div class="content-header-right"><a href="why-feature.php" class="btn btn-primary btn-sm">View All</a></div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($error_message): ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php endif; ?>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Title <span>*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Icon Image</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="icon"> (optional custom icon)
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">FA Icon Class</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="icon_class" value="<?php echo isset($_POST['icon_class']) ? htmlspecialchars($_POST['icon_class']) : 'fa-star'; ?>" placeholder="fa-trophy">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Sort Order</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="sort_order" value="<?php echo isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0; ?>">
                                <p class="help-block">1–3 left side, 4–6 right side</p>
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
