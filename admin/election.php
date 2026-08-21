<?php require_once('header.php'); ?>

<?php

if(isset($_GET['success'])) {
    $success_message = 'Candidate added successfully!';
} elseif(isset($_GET['updated'])) {
    $success_message = 'Candidate updated successfully!';
} elseif(isset($_GET['deleted'])) {
    $success_message = 'Candidate deleted successfully!';
}

?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Election Candidates</h1>
    </div>

    <div class="content-header-right">
        <a href="election-add.php" class="btn btn-primary btn-sm">
            Add Candidate
        </a>
        <a href="election-export.php" class="btn btn-success btn-sm">
    <i class="fa fa-download"></i> Export CSV
</a>
    </div>
</section>

<section class="content">

<div class="row">
<div class="col-md-12">

<?php if(!empty($error_message)): ?>
<div class="callout callout-danger">
<p><?php echo $error_message; ?></p>
</div>
<?php endif; ?>

<?php if(!empty($success_message)): ?>
<div class="callout callout-success">
<p><?php echo $success_message; ?></p>
</div>
<?php endif; ?>

<div class="box box-info">

<div class="box-body table-responsive">

<table id="example1" class="table table-bordered table-hover table-striped">

<thead>
<tr>

<th width="40">SN</th>
<th width="90">Photo</th>
<th>Candidate Name</th>
<th>Election Post</th>
<th width="90">Votes</th>
<th width="70">Rank</th>
<th width="80">Status</th>
<th width="160">Action</th>

</tr>
</thead>

<tbody>

<?php

$i = 0;

$statement = $pdo->prepare("
    SELECT *,
           DENSE_RANK() OVER (
               PARTITION BY election_post
               ORDER BY vote_count DESC
           ) AS rank_position
    FROM tbl_elections
    ORDER BY election_post ASC, rank_position ASC, candidate_name ASC
");

$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $row)
{
    $i++;
?>

<tr>

<td><?php echo $i; ?></td>

<td>

<?php if(!empty($row['candidate_image'])): ?>

<img
src="<?php echo adminUploadUrl($row['candidate_image']); ?>"
style="width:60px;height:70px;object-fit:cover;">

<?php else: ?>

<span class="text-muted">No Image</span>

<?php endif; ?>

</td>

<td>

<?php echo htmlspecialchars($row['candidate_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['election_post']); ?>

</td>

<td>

<span class="badge bg-green">

<?php echo (int)$row['vote_count']; ?>

</span>

</td>
<td>
<?php
switch($row['rank_position'])
{
    case 1:
        echo '<span class="badge bg-yellow">1</span>';
        break;

    case 2:
        echo '<span class="badge bg-gray">2</span>';
        break;

    case 3:
        echo '<span class="badge bg-orange">3</span>';
        break;

    default:
        echo '<span class="badge bg-blue">'.$row['rank_position'].'th</span>';
}
?>
</td>

<td>

<?php
if($row['is_active']==1)
{
    echo '<span class="label label-success">Active</span>';
}
else
{
    echo '<span class="label label-danger">Inactive</span>';
}
?>

</td>

<td>

<a
href="election-edit.php?id=<?php echo $row['id']; ?>"
class="btn btn-primary btn-xs">

Edit

</a>

<a
href="#"
class="btn btn-danger btn-xs"
data-href="election-delete.php?id=<?php echo $row['id']; ?>"
data-toggle="modal"
data-target="#confirm-delete">

Delete

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>
</div>

</section>

<div class="modal fade" id="confirm-delete">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<button
type="button"
class="close"
data-dismiss="modal">

&times;

</button>

<h4 class="modal-title">

Delete Confirmation

</h4>

</div>

<div class="modal-body">

Are you sure you want to delete this candidate?

</div>

<div class="modal-footer">

<button
type="button"
class="btn btn-default"
data-dismiss="modal">

Cancel

</button>

<a class="btn btn-danger btn-ok">

Delete

</a>

</div>

</div>

</div>

</div>

<?php require_once('footer.php'); ?>