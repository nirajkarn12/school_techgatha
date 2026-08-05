<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT * FROM tbl_birthday_student WHERE id=?");
$statement->execute(array($id));
$row = $statement->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    header('location: logout.php');
    exit;
}

$template_id = (int)$row['template_id'];
$name = $row['name'];
$class_name = $row['class_name'];
$birthday_date = $row['birthday_date'];
$details = $row['details'];
$student_image = $row['student_image'];
$sort_order = (int)$row['sort_order'];
$status = $row['status'];

if (isset($_POST['form1'])) {
    $valid = 1;
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $valid = 0;
        $error_message .= 'Student name is required<br>';
    }

    $class_name = trim($_POST['class_name'] ?? '');
    $birthday_date = trim($_POST['birthday_date'] ?? '');
    $details = trim($_POST['details'] ?? '');
    $template_id = (int)($_POST['template_id'] ?? 0);

    $path = $_FILES['student_image']['name'] ?? '';
    $tmp = $_FILES['student_image']['tmp_name'] ?? '';
    $err = (int)($_FILES['student_image']['error'] ?? UPLOAD_ERR_NO_FILE);
    $hasNewImage = ($path !== '' && $err === UPLOAD_ERR_OK && is_uploaded_file($tmp));
    if ($hasNewImage) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            $valid = 0;
            $error_message .= 'Student image must be jpg, jpeg, png, gif or webp<br>';
            $hasNewImage = false;
        }
    } elseif ($path !== '' && $err !== UPLOAD_ERR_NO_FILE) {
        $valid = 0;
        $error_message .= 'Student image upload failed. Try a smaller file.<br>';
    }

    if ($template_id <= 0) {
        $template = $pdo->query("SELECT id FROM tbl_birthday_template WHERE status='Active' ORDER BY sort_order ASC, id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($template) {
            $template_id = (int)$template['id'];
        }
    }

    if ($valid == 1) {
        $final_name = $student_image;
        if ($hasNewImage) {
            $final_name = adminUniqueUploadName('birthday-student', $ext, $id);
            if (!adminMoveUploadedFile($tmp, $final_name)) {
                $valid = 0;
                $error_message .= 'Could not save student image. Check uploads folder permissions.<br>';
                $final_name = $student_image;
            }
        }

        if ($valid == 1) {
            $statement = $pdo->prepare("UPDATE tbl_birthday_student SET template_id=?, name=?, class_name=?, birthday_date=?, details=?, student_image=?, status=?, sort_order=? WHERE id=?");
            $statement->execute(array(
                $template_id,
                $name,
                $class_name,
                $birthday_date,
                $details,
                $final_name,
                ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
                (int)($_POST['sort_order'] ?? 0),
                $id,
            ));
            if ($hasNewImage && $student_image !== '' && $student_image !== $final_name) {
                adminDeleteUploadIfUnused($pdo, $student_image, 'tbl_birthday_student', 'student_image', $id);
            }
            header('location: birthday.php?updated=1');
            exit;
        }
    }

    $sort_order = (int)($_POST['sort_order'] ?? $sort_order);
    $status = $_POST['status'] ?? $status;
}

$templates = $pdo->query("SELECT * FROM tbl_birthday_template WHERE status='Active' ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="content-header">
    <div class="content-header-left"><h1>Edit Birthday Student</h1></div>
    <div class="content-header-right"><a href="birthday.php" class="btn btn-primary btn-sm">View All</a></div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($error_message): ?><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div><?php endif; ?>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Student Name <span>*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Class</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="class_name" value="<?php echo htmlspecialchars($class_name); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Birthday Date</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="birthday_date" value="<?php echo htmlspecialchars($birthday_date); ?>" placeholder="e.g. 2026-08-04">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Details</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="details" rows="3"><?php echo htmlspecialchars($details); ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Current Image</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <?php if ($student_image !== '' && is_file(adminUploadsPath($student_image))): ?>
                                    <img src="<?php echo htmlspecialchars(adminUploadUrl($student_image)); ?>" style="width:90px;height:110px;object-fit:cover;background:#f2f2f2;padding:6px;">
                                <?php else: ?>
                                    <span class="text-muted">No image on file.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Replace Image</label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="student_image"> (optional)
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Template</label>
                            <div class="col-sm-4">
                                <select name="template_id" class="form-control">
                                    <option value="0">Use default/first active</option>
                                    <?php foreach ($templates as $template): ?>
                                        <option value="<?php echo (int)$template['id']; ?>" <?php echo (int)$template_id === (int)$template['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($template['title']); ?></option>
                                    <?php endforeach; ?>
                                </select>
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