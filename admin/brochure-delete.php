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

$id = (int)$_REQUEST['id'];
$image = $result[0]['image'];
$file = $result[0]['file'];

$statement = $pdo->prepare("DELETE FROM tbl_brochure WHERE id=?");
$statement->execute(array($id));

if ($image !== '' && is_file('../assets/uploads/' . $image)) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM tbl_brochure WHERE image=?");
    $check->execute(array($image));
    if ((int)$check->fetchColumn() === 0) {
        unlink('../assets/uploads/' . $image);
    }
}
if ($file !== '' && is_file('../assets/uploads/' . $file)) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM tbl_brochure WHERE file=?");
    $check->execute(array($file));
    if ((int)$check->fetchColumn() === 0) {
        unlink('../assets/uploads/' . $file);
    }
}

header('location: brochure.php?deleted=1');
exit;
