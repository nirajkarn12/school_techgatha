<?php require_once('header.php'); ?>

<?php

if(!isset($_REQUEST['id']))
{
    header('location:logout.php');
    exit;
}

$id = (int)$_REQUEST['id'];

$statement = $pdo->prepare("SELECT * FROM tbl_elections WHERE id=?");
$statement->execute(array($id));

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

if(!$result)
{
    header('location:logout.php');
    exit;
}

$row = $result[0];

$candidate_name  = $row['candidate_name'];
$candidate_image = $row['candidate_image'];
$election_post   = $row['election_post'];
$is_active       = $row['is_active'];

if(isset($_POST['form1']))
{
    $valid = 1;

    $candidate_name = trim($_POST['candidate_name'] ?? '');
    $election_post  = trim($_POST['election_post'] ?? '');

    if($candidate_name == '')
    {
        $valid = 0;
        $error_message .= "Candidate Name is required.<br>";
    }

    if($election_post == '')
    {
        $valid = 0;
        $error_message .= "Election Post is required.<br>";
    }

    $path     = $_FILES['candidate_image']['name'] ?? '';
    $path_tmp = $_FILES['candidate_image']['tmp_name'] ?? '';
    $imgErr   = $_FILES['candidate_image']['error'] ?? UPLOAD_ERR_NO_FILE;

    $hasNewPhoto = ($path != '' && $imgErr == UPLOAD_ERR_OK && is_uploaded_file($path_tmp));

    $ext = '';

    if($hasNewPhoto)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if(!in_array($ext,['jpg','jpeg','png','gif','webp']))
        {
            $valid = 0;
            $error_message .= "Photo must be JPG, JPEG, PNG, GIF or WEBP.<br>";
            $hasNewPhoto = false;
        }
    }

    if($valid == 1)
    {
        $final_name = $candidate_image;

        if($hasNewPhoto)
        {
            $final_name = adminUniqueUploadName('candidate',$ext,$id);

            if(!adminMoveUploadedFile($path_tmp,$final_name))
            {
                $valid = 0;
                $error_message .= "Unable to upload image.<br>";
            }
        }

        if($valid == 1)
        {
            $statement = $pdo->prepare("
                UPDATE tbl_elections
                SET
                    candidate_name=?,
                    candidate_image=?,
                    election_post=?,
                    is_active=?
                WHERE id=?
            ");

            $statement->execute(array(
                $candidate_name,
                $final_name,
                $election_post,
                (int)($_POST['is_active'] ?? 1),
                $id
            ));

            if($hasNewPhoto && $candidate_image != '' && $candidate_image != $final_name)
            {
                adminDeleteUploadIfUnused(
                    $pdo,
                    $candidate_image,
                    'tbl_elections',
                    'candidate_image',
                    $id
                );
            }

            header("location:election.php?updated=1");
            exit;
        }
    }

    $is_active = (int)($_POST['is_active'] ?? $is_active);
}

?>

<section class="content-header">

<div class="content-header-left">
<h1>Edit Election Candidate</h1>
</div>

<div class="content-header-right">
<a href="election.php" class="btn btn-primary btn-sm">
View All
</a>
</div>

</section>

<section class="content">

<div class="row">

<div class="col-md-12">

<?php if($error_message){ ?>

<div class="callout callout-danger">
<p><?php echo $error_message; ?></p>
</div>

<?php } ?>

<form
class="form-horizontal"
action=""
method="post"
enctype="multipart/form-data">

<div class="box box-info">

<div class="box-body">

<div class="form-group">

<label class="col-sm-2 control-label">
Candidate Name <span>*</span>
</label>

<div class="col-sm-6">

<input
type="text"
name="candidate_name"
class="form-control"
value="<?php echo htmlspecialchars($candidate_name); ?>">

</div>

</div>

<div class="form-group">

<label class="col-sm-2 control-label">
Current Photo
</label>

<div class="col-sm-6">

<?php if($candidate_image != '' && is_file(adminUploadsPath($candidate_image))){ ?>

<img
src="<?php echo htmlspecialchars(adminUploadUrl($candidate_image)); ?>"
style="width:90px;height:120px;object-fit:cover;">

<?php } else { ?>

<span class="text-muted">
No Image Available
</span>

<?php } ?>

</div>

</div>

<div class="form-group">

<label class="col-sm-2 control-label">
Change Photo
</label>

<div class="col-sm-6">

<input
type="file"
name="candidate_image">

(optional)

</div>

</div>

<div class="form-group">

<label class="col-sm-2 control-label">
Election Post <span>*</span>
</label>

<div class="col-sm-4">

<input
type="text"
name="election_post"
class="form-control"
value="<?php echo htmlspecialchars($election_post); ?>">

</div>

</div>

<div class="form-group">

<label class="col-sm-2 control-label">
Status
</label>

<div class="col-sm-3">

<select
name="is_active"
class="form-control">

<option value="1" <?php if($is_active==1) echo "selected"; ?>>
Active
</option>

<option value="0" <?php if($is_active==0) echo "selected"; ?>>
Inactive
</option>

</select>

</div>

</div>

<div class="form-group">

<label class="col-sm-2 control-label"></label>

<div class="col-sm-6">

<button
type="submit"
name="form1"
class="btn btn-success">

Update

</button>

</div>

</div>

</div>

</div>

</form>

</div>

</div>

</section>

<?php require_once('footer.php'); ?>