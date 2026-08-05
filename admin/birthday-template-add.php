<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

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
    if ($path === '' || $err !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
        $valid = 0;
        $error_message .= 'Template image is required<br>';
    } else {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            $valid = 0;
            $error_message .= 'Template image must be jpg, jpeg, png, gif or webp<br>';
        }
    }

    if ($valid == 1) {
        $final_name = adminUniqueUploadName('birthday-template', $ext);
        if (!adminMoveUploadedFile($tmp, $final_name)) {
            $valid = 0;
            $error_message .= 'Could not save template image. Check uploads folder permissions.<br>';
        }
    }

    if ($valid == 1) {
        $statement = $pdo->prepare("INSERT INTO tbl_birthday_template (title, template_image, output_x, output_y, output_width, output_height, status, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $statement->execute(array(
            $title,
            $final_name,
            (int)($_POST['output_x'] ?? 0),
            (int)($_POST['output_y'] ?? 0),
            (int)($_POST['output_width'] ?? 0),
            (int)($_POST['output_height'] ?? 0),
            ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
            (int)($_POST['sort_order'] ?? 0),
        ));
        header('location: birthday-template.php?success=1');
        exit;
    }
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Add Birthday Template</h1></div>
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
                                <input type="text" class="form-control" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Template Image <span>*</span></label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="template_image">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Frame X</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="output_x" value="<?php echo isset($_POST['output_x']) ? (int)$_POST['output_x'] : 285; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Frame Y</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="output_y" value="<?php echo isset($_POST['output_y']) ? (int)$_POST['output_y'] : 205; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Frame Width</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="output_width" value="<?php echo isset($_POST['output_width']) ? (int)$_POST['output_width'] : 510; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Frame Height</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="output_height" value="<?php echo isset($_POST['output_height']) ? (int)$_POST['output_height'] : 590; ?>">
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