<?php require_once('header.php'); ?>

<?php

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

    $ext = '';

    if($path == '' || $imgErr != UPLOAD_ERR_OK || !is_uploaded_file($path_tmp))
    {
        $valid = 0;
        $error_message .= "Candidate Photo is required.<br>";
    }
    else
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if(!in_array($ext,['jpg','jpeg','png','gif','webp']))
        {
            $valid = 0;
            $error_message .= "Photo must be JPG, JPEG, PNG, GIF or WEBP.<br>";
        }
    }

    if($valid == 1)
    {
        $final_name = adminUniqueUploadName('candidate',$ext);

        if(!adminMoveUploadedFile($path_tmp,$final_name))
        {
            $error_message .= "Unable to upload image.<br>";
        }
        else
        {
            $statement = $pdo->prepare("
                INSERT INTO tbl_elections
                (
                    candidate_name,
                    candidate_image,
                    election_post,
                    vote_count,
                    is_active,
                    created_at
                )
                VALUES (?,?,?,?,?,NOW())
            ");

            $statement->execute([
                $candidate_name,
                $final_name,
                $election_post,
                0,
                (int)($_POST['is_active'] ?? 1)
            ]);

            header("location:election.php?success=1");
            exit;
        }
    }

}

?>

<section class="content-header">

<div class="content-header-left">
<h1>Add Election Candidate</h1>
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
value="<?php echo htmlspecialchars($_POST['candidate_name'] ?? ''); ?>">

</div>

</div>

<div class="form-group">

<label class="col-sm-2 control-label">
Candidate Photo <span>*</span>
</label>

<div class="col-sm-6" style="padding-top:5px;">

<input type="file" name="candidate_image">

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
placeholder="President"
value="<?php echo htmlspecialchars($_POST['election_post'] ?? ''); ?>">

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

<option value="1">Active</option>

<option value="0">Inactive</option>

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

Submit

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