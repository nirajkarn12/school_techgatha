<?php require_once('header.php'); ?>

<?php
if (isset($_POST['form1'])) {
    $valid = 1;

    $path = $_FILES['logo']['name'] ?? '';
    $path_tmp = $_FILES['logo']['tmp_name'] ?? '';
    $imgErr = (int)($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE);
    $ext = '';

    if ($path === '' || $imgErr !== UPLOAD_ERR_OK || !is_uploaded_file($path_tmp)) {
        $valid = 0;
        $error_message .= 'Logo is required<br>';
    } else {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'), true)) {
            $valid = 0;
            $error_message .= 'Logo must be jpg, jpeg, png, gif, webp or svg<br>';
        }
    }

    if ($valid == 1) {
        $final_name = adminUniqueUploadName('client', $ext);
        if (!adminMoveUploadedFile($path_tmp, $final_name)) {
            $error_message .= 'Could not save logo. Check uploads folder permissions.<br>';
        } else {
            $statement = $pdo->prepare("INSERT INTO tbl_client (name, logo, website_url, status, sort_order, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $statement->execute(array(
                trim($_POST['name'] ?? ''),
                $final_name,
                trim($_POST['website_url'] ?? ''),
                ($_POST['status'] ?? '') === 'Inactive' ? 'Inactive' : 'Active',
                (int)($_POST['sort_order'] ?? 0),
            ));
            header('location: client.php?success=1');
            exit;
        }
    }
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Add Client Logo</h1></div>
    <div class="content-header-right"><a href="client.php" class="btn btn-primary btn-sm">View All</a></div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($error_message): ?>
            <div class="callout callout-danger"><p><?php echo $error_message; ?></p></div>
            <?php endif; ?>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" placeholder="Internal label (optional)">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Logo <span>*</span></label>
                            <div class="col-sm-6" style="padding-top:5px;">
                                <input type="file" name="logo"> (jpg/png/gif/webp/svg)
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Website URL</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="website_url" value="<?php echo isset($_POST['website_url']) ? htmlspecialchars($_POST['website_url']) : ''; ?>" placeholder="https:// (optional)">
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
                            <label class="col-sm-2 control-label">Sort Order</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="sort_order" value="<?php echo isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0; ?>">
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
