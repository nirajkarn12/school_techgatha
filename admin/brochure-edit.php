<?php require_once('header.php'); ?>

<?php
ensureBrochureTable($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

$statement = $pdo->prepare("SELECT * FROM tbl_brochure WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
if (!$result) {
    header('location: logout.php');
    exit;
}
$row = $result[0];
$id = (int)$row['id'];
$title = $row['title'];
$year = $row['year'];
$image = $row['image'];
$file = $row['file'];
$sort_order = (int)$row['sort_order'];
$status = $row['status'];

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
    $hasNewImage = ($imgName !== '' && $imgErr === UPLOAD_ERR_OK && is_uploaded_file($imgTmp));
    if ($hasNewImage) {
        $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        if (!in_array($imgExt, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            $valid = 0;
            $error_message .= 'Image must be jpg, jpeg, png, gif or webp<br>';
            $hasNewImage = false;
        }
    } elseif ($imgName !== '' && $imgErr !== UPLOAD_ERR_NO_FILE) {
        $valid = 0;
        $error_message .= 'Image upload failed. Try a smaller file.<br>';
    }

    $fileName = $_FILES['pdf_file']['name'] ?? '';
    $fileTmp = $_FILES['pdf_file']['tmp_name'] ?? '';
    $fileErr = (int)($_FILES['pdf_file']['error'] ?? UPLOAD_ERR_NO_FILE);
    $fileExt = '';
    $hasNewFile = ($fileName !== '' && $fileErr === UPLOAD_ERR_OK && is_uploaded_file($fileTmp));
    if ($hasNewFile) {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExt, array('pdf', 'doc', 'docx'), true)) {
            $valid = 0;
            $error_message .= 'File must be pdf, doc or docx<br>';
            $hasNewFile = false;
        }
    } elseif ($fileName !== '' && $fileErr !== UPLOAD_ERR_NO_FILE) {
        $valid = 0;
        $error_message .= 'File upload failed. Try a smaller file.<br>';
    }

    if ($valid == 1) {
        $finalImage = $image;
        if ($hasNewImage) {
            $finalImage = adminUniqueUploadName('brochure', $imgExt, $id);
            if (!adminMoveUploadedFile($imgTmp, $finalImage)) {
                $valid = 0;
                $error_message .= 'Could not save cover image. Check uploads folder permissions.<br>';
                $finalImage = $image;
            }
        }

        $finalFile = $file;
        if ($valid == 1 && $hasNewFile) {
            $finalFile = adminUniqueUploadName('brochure-file', $fileExt, $id);
            if (!adminMoveUploadedFile($fileTmp, $finalFile)) {
                $valid = 0;
                $error_message .= 'Could not save PDF/file. Check uploads folder permissions.<br>';
                $finalFile = $file;
            }
        }

        if ($valid == 1) {
            $statement = $pdo->prepare("UPDATE tbl_brochure SET title=?, year=?, image=?, file=?, sort_order=?, status=? WHERE id=?");
            $statement->execute(array(
                $title,
                trim($_POST['year'] ?? ''),
                $finalImage,
                $finalFile,
                (int)($_POST['sort_order'] ?? 0),
                ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
                $id,
            ));
            if ($hasNewImage && $image !== '' && $image !== $finalImage) {
                adminDeleteUploadIfUnused($pdo, $image, 'tbl_brochure', 'image', $id);
            }
            if ($hasNewFile && $file !== '' && $file !== $finalFile) {
                adminDeleteUploadIfUnused($pdo, $file, 'tbl_brochure', 'file', $id);
            }
            header('location: brochure.php?updated=1');
            exit;
        }
    }

    $year = trim($_POST['year'] ?? $year);
    $sort_order = (int)($_POST['sort_order'] ?? $sort_order);
    $status = $_POST['status'] ?? $status;
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Edit Brochure</h1></div>
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
                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($title); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Year</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="year" value="<?php echo htmlspecialchars($year); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Current Image</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <?php if ($image !== '' && is_file('../assets/uploads/' . $image)): ?>
                                    <img src="<?php echo htmlspecialchars(adminUploadUrl($image)); ?>" style="max-width:120px;max-height:140px;object-fit:contain;background:#f2f2f2;padding:6px;">
                                <?php else: ?>
                                    <span class="text-muted">No image on file — please upload a new cover image.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Change Image</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.webp">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Current File</label>
                            <div class="col-sm-6" style="padding-top:7px;">
                                <?php echo $file !== '' ? htmlspecialchars($file) : '—'; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Change File</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="pdf_file" accept=".pdf,.doc,.docx">
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
