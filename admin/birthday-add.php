<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

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
    if ($path === '' || $err !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
        $valid = 0;
        $error_message .= 'Student image is required<br>';
    } else {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            $valid = 0;
            $error_message .= 'Student image must be jpg, jpeg, png, gif or webp<br>';
        }
    }

    if ($template_id <= 0) {
        $template = $pdo->query("SELECT id FROM tbl_birthday_template WHERE status='Active' ORDER BY sort_order ASC, id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($template) {
            $template_id = (int)$template['id'];
        }
    }

    if ($valid == 1) {
        $final_name = adminUniqueUploadName('birthday-student', $ext);
        if (!adminMoveUploadedFile($tmp, $final_name)) {
            $valid = 0;
            $error_message .= 'Could not save student image. Check uploads folder permissions.<br>';
        }
    }

    if ($valid == 1) {
        $statement = $pdo->prepare("INSERT INTO tbl_birthday_student (template_id, name, class_name, birthday_date, details, student_image, generated_image, status, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, '', ?, ?, NOW())");
        $statement->execute(array(
            $template_id,
            $name,
            $class_name,
            $birthday_date,
            $details,
            $final_name,
            ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
            (int)($_POST['sort_order'] ?? 0),
        ));
        header('location: birthday.php?success=1');
        exit;
    }
}

$templates = $pdo->query("SELECT * FROM tbl_birthday_template WHERE status='Active' ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="content-header">
    <div class="content-header-left"><h1>Add Birthday Student</h1></div>
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
                                <input type="text" class="form-control" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Class</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="class_name" value="<?php echo isset($_POST['class_name']) ? htmlspecialchars($_POST['class_name']) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Birthday Date</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="birthday_date" value="<?php echo isset($_POST['birthday_date']) ? htmlspecialchars($_POST['birthday_date']) : ''; ?>" placeholder="e.g. 2026-08-04">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Details</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="details" rows="3"><?php echo isset($_POST['details']) ? htmlspecialchars($_POST['details']) : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Student Image <span>*</span></label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="student_image">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Template</label>
                            <div class="col-sm-4">
                                <select name="template_id" class="form-control">
                                    <option value="0">Use default/first active</option>
                                    <?php foreach ($templates as $template): ?>
                                        <option value="<?php echo (int)$template['id']; ?>" <?php echo isset($_POST['template_id']) && (int)$_POST['template_id'] === (int)$template['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($template['title']); ?></option>
                                    <?php endforeach; ?>
                                </select>
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