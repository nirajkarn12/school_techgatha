<?php require_once('header.php'); ?>

<?php
ensureBrochureTable($pdo);

if (isset($_POST['form1'])) {
    $valid = 1;
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        $valid = 0;
        $error_message .= 'Title is required<br>';
    }

    $imgName = $_FILES['image']['name'] ?? '';
    $imgTmp = $_FILES['image']['tmp_name'] ?? '';
    $imgErr = (int)($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE);
    $imgExt = '';
    if ($imgName === '' || $imgErr !== UPLOAD_ERR_OK || !is_uploaded_file($imgTmp)) {
        $valid = 0;
        $error_message .= 'Cover image is required<br>';
    } else {
        $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        if (!in_array($imgExt, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            $valid = 0;
            $error_message .= 'Image must be jpg, jpeg, png, gif or webp<br>';
        }
    }

    $fileName = $_FILES['pdf_file']['name'] ?? '';
    $fileTmp = $_FILES['pdf_file']['tmp_name'] ?? '';
    $fileErr = (int)($_FILES['pdf_file']['error'] ?? UPLOAD_ERR_NO_FILE);
    $fileExt = '';
    $finalFile = '';
    if ($fileName !== '' && $fileErr === UPLOAD_ERR_OK && is_uploaded_file($fileTmp)) {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExt, array('pdf', 'doc', 'docx'), true)) {
            $valid = 0;
            $error_message .= 'File must be pdf, doc or docx<br>';
        }
    }

    if ($valid == 1) {
        $statement = $pdo->prepare("INSERT INTO tbl_brochure (title, year, image, file, sort_order, status, created_at) VALUES (?, ?, '', '', ?, ?, NOW())");
        $statement->execute(array(
            $title,
            trim($_POST['year'] ?? ''),
            (int)($_POST['sort_order'] ?? 0),
            ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
        ));
        $newId = (int) $pdo->lastInsertId();

        $finalImage = adminUniqueUploadName('brochure', $imgExt, $newId);
        if (!adminMoveUploadedFile($imgTmp, $finalImage)) {
            $pdo->prepare("DELETE FROM tbl_brochure WHERE id=?")->execute(array($newId));
            $error_message .= 'Could not save cover image. Check uploads folder permissions.<br>';
        } else {
            if ($fileName !== '' && $fileErr === UPLOAD_ERR_OK && is_uploaded_file($fileTmp)) {
                $finalFile = adminUniqueUploadName('brochure-file', $fileExt, $newId);
                if (!adminMoveUploadedFile($fileTmp, $finalFile)) {
                    $finalFile = '';
                }
            }

            $statement = $pdo->prepare("UPDATE tbl_brochure SET image=?, file=? WHERE id=?");
            $statement->execute(array($finalImage, $finalFile, $newId));
            header('location: brochure.php?success=1');
            exit;
        }
    }
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Add Brochure</h1></div>
    <div class="content-header-right"><a href="brochure.php" class="btn btn-primary btn-sm">View All</a></div>
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
                                <input type="text" class="form-control" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" placeholder="School Prospectus">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Year</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="year" value="<?php echo isset($_POST['year']) ? htmlspecialchars($_POST['year']) : date('Y'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Cover Image <span>*</span></label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.webp">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">PDF / File</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="pdf_file" accept=".pdf,.doc,.docx"> (optional download)
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
