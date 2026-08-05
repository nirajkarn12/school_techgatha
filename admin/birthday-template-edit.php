<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT * FROM tbl_birthday_template WHERE id=?");
$statement->execute(array($id));
$row = $statement->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    header('location: logout.php');
    exit;
}

$title = $row['title'];
$template_image = $row['template_image'];
$output_x = (int)$row['output_x'];
$output_y = (int)$row['output_y'];
$output_width = (int)$row['output_width'];
$output_height = (int)$row['output_height'];
$sort_order = (int)$row['sort_order'];
$status = $row['status'];

if (isset($_POST['form1'])) {
    $valid = 1;
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        $valid = 0;
        $error_message .= 'Title is required<br>';
    }

    $path = $_FILES['template_image']['name'] ?? '';
    $tmp = $_FILES['template_image']['tmp_name'] ?? '';
    $err = (int)($_FILES['template_image']['error'] ?? UPLOAD_ERR_NO_FILE);
    $hasNewImage = ($path !== '' && $err === UPLOAD_ERR_OK && is_uploaded_file($tmp));
    if ($hasNewImage) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            $valid = 0;
            $error_message .= 'Template image must be jpg, jpeg, png, gif or webp<br>';
            $hasNewImage = false;
        }
    } elseif ($path !== '' && $err !== UPLOAD_ERR_NO_FILE) {
        $valid = 0;
        $error_message .= 'Template image upload failed. Try a smaller file.<br>';
    }

    if ($valid == 1) {
        $final_name = $template_image;
        if ($hasNewImage) {
            $final_name = adminUniqueUploadName('birthday-template', $ext, $id);
            if (!adminMoveUploadedFile($tmp, $final_name)) {
                $valid = 0;
                $error_message .= 'Could not save template image. Check uploads folder permissions.<br>';
                $final_name = $template_image;
            }
        }

        if ($valid == 1) {
            $statement = $pdo->prepare("UPDATE tbl_birthday_template SET title=?, template_image=?, output_x=?, output_y=?, output_width=?, output_height=?, status=?, sort_order=? WHERE id=?");
            $statement->execute(array(
                $title,
                $final_name,
                (int)($_POST['output_x'] ?? 0),
                (int)($_POST['output_y'] ?? 0),
                (int)($_POST['output_width'] ?? 0),
                (int)($_POST['output_height'] ?? 0),
                ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
                (int)($_POST['sort_order'] ?? 0),
                $id,
            ));
            if ($hasNewImage && $template_image !== '' && $template_image !== $final_name) {
                adminDeleteUploadIfUnused($pdo, $template_image, 'tbl_birthday_template', 'template_image', $id);
            }
            header('location: birthday-template.php?updated=1');
            exit;
        }
    }

    $output_x = (int)($_POST['output_x'] ?? $output_x);
    $output_y = (int)($_POST['output_y'] ?? $output_y);
    $output_width = (int)($_POST['output_width'] ?? $output_width);
    $output_height = (int)($_POST['output_height'] ?? $output_height);
    $sort_order = (int)($_POST['sort_order'] ?? $sort_order);
    $status = $_POST['status'] ?? $status;
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Edit Birthday Template</h1></div>
    <div class="content-header-right"><a href="birthday-template.php" class="btn btn-primary btn-sm">View All</a></div>
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
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($title); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Current Image</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <?php if ($template_image !== '' && is_file(adminUploadsPath($template_image))): ?>
                                    <img src="<?php echo htmlspecialchars(adminUploadUrl($template_image)); ?>" style="width:110px;height:110px;object-fit:cover;background:#f2f2f2;padding:6px;">
                                <?php else: ?>
                                    <span class="text-muted">No image on file.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Replace Image</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="template_image"> (optional)
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Frame X</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="output_x" value="<?php echo (int)$output_x; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Frame Y</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="output_y" value="<?php echo (int)$output_y; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Frame Width</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="output_width" value="<?php echo (int)$output_width; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Frame Height</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="output_height" value="<?php echo (int)$output_height; ?>">
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